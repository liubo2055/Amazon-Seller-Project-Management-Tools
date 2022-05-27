<?php

namespace Hui\Xproject\Services\UserImporter;

use ErrorException;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\PaymentManager\PaymentManager;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

class UserImporter{
  const COLUMNS=[
    'name'=>self::COLUMN_NAME,
    'cell'=>self::COLUMN_PHONE,
    'notes'=>self::COLUMN_NOTES,
    'company name'=>self::COLUMN_COMPANY_NAME,
    'email'=>self::COLUMN_EMAIL,
    'qq'=>self::COLUMN_QQ,
    'website url'=>self::COLUMN_COMPANY_URL,
    'register date'=>self::COLUMN_REGISTER_DATE,
    'alipay id'=>self::COLUMN_ALIPAY_ID,
    'wechat id'=>self::COLUMN_WECHAT_ID
  ];
  const COLUMN_ALIPAY_ID='alipayId';
  const COLUMN_COMPANY_NAME='companyName';
  const COLUMN_COMPANY_URL='companyUrl';
  const COLUMN_EMAIL='email';
  const COLUMN_NAME='name';
  const COLUMN_NOTES='notes';
  const COLUMN_PHONE='phone';
  const COLUMN_QQ='qq';
  const COLUMN_REGISTER_DATE='registerDate';
  const COLUMN_WECHAT_ID='wechatId';

  public function __construct(PaymentManager $paymentManager){
    $this->paymentManager=$paymentManager;
  }

  /**
   * @param string $contents
   * @return ImportedUser[]
   */
  public function extract(string $contents):array{
    try{
      $reader=$this->open($contents,$path);
    }
    catch(ErrorException $e){
      logger()->error(sprintf('Error while reading the Excel file: "%s"',$e->getMessage()));

      if($path&&!@unlink($path))
        logger()->error(sprintf('Cannot delete "%s"',$path));

      return [];
    }

    $users=[];

    foreach($reader->getAllSheets() as $sheet){
      $header=$this->parseHeader($sheet);
      if($this->validHeader($header)){
        $users=$this->importFromSheet($sheet,$header);
        break;
      }
    }

    if(!@unlink($path))
      logger()->error(sprintf('Cannot delete "%s"',$path));

    return $users;
  }

  /**
   * @param ImportedUser[] $importedUsers
   * @return ImportResult
   * @throws UserImporterException
   */
  public function import(array $importedUsers):ImportResult{
    $result=new ImportResult;
    $result->imported=0;
    $result->errors=0;
    $result->existing=0;

    foreach($importedUsers as $importedUser)
      if($importedUser->imported){
        $user=$importedUser->user;
        if(User::findByEmail($user->email,true))
          throw new UserImporterException(sprintf('A user with email "%s" already exists',$user->email));

        $user->save();

        $result->imported++;
      }
      else if($importedUser->error)
        $result->errors++;
      else
        $result->existing++;

    return $result;
  }

  private function open(string $contents,string &$path=null):PHPExcel{
    // Make sure the file is saved in the local disk

    $directory=storage_path('temp');
    if(!is_dir($directory))
      if(!@mkdir($directory,0777,true))
        throw new UserImporterException(sprintf('Cannot create "%s"',$directory));

    $path=tempnam($directory,'xproject');

    if(!@file_put_contents($path,$contents))
      throw new UserImporterException(sprintf('Cannot save contents into "%s"',$path));

    return PHPExcel_IOFactory::load($path);
  }

  private function parseHeader(PHPExcel_Worksheet $sheet):array{
    $range=sprintf('A1:%s1',$sheet->getHighestDataColumn());
    $rows=$sheet->rangeToArray($range);

    $header=[];

    foreach($rows[0] as $index=>$cell){
      $cell=mb_strtolower($cell);

      if(isset(static::COLUMNS[$cell])){
        $column=static::COLUMNS[$cell];

        // If a column is repeated, use only the first occurrence
        if(!in_array($column,$header))
          $header[$index]=$column;
      }
    }

    return $header;
  }

  private function validHeader(array $header):bool{
    $requiredColumns=[static::COLUMN_EMAIL];

    return count(array_intersect($header,$requiredColumns))===count($requiredColumns);
  }

  /**
   * @param PHPExcel_Worksheet $sheet
   * @param string[]           $header
   * @return ImportedUser[]
   */
  private function importFromSheet(PHPExcel_Worksheet $sheet,array $header):array{
    $range=sprintf('A2:%s%u',
      $sheet->getHighestDataColumn(),
      $sheet->getHighestDataRow());
    $rows=$sheet->rangeToArray($range);

    $importedUsers=[];
    $importedUserEmails=[];

    foreach($rows as $index=>$row){
      $user=$this->importFromRow($row,$header);

      $importedUser=new ImportedUser;
      $importedUser->row=$index+2;
      $importedUser->imported=false;
      $importedUser->user=$user;
      $importedUser->error=$this->checkUserError($user);

      if(!$importedUser->error)
        if(!User::findByEmail($user->email,true)){
          $emailLowerCase=mb_strtolower($user->email);

          if(!isset($importedUserEmails[$emailLowerCase])){
            $importedUser->imported=true;
            $importedUserEmails[$emailLowerCase]=true;
          }
          else
            $importedUser->error=_ix('A user with the same email address has already been found in this file','User importer');
        }

      $importedUsers[]=$importedUser;
    }

    return $importedUsers;
  }

  private function importFromRow(array $row,array $header):User{
    $user=$this->newUser();

    foreach($header as $index=>$column){
      if(!isset($row[$index]))
        continue;

      $value=$row[$index];
      if(is_string($value))
        $value=trim($value);

      switch($column){
        case static::COLUMN_COMPANY_NAME:
          $user->company_name=$value;
          break;
        case static::COLUMN_COMPANY_URL:
          $user->company_url=$value;
          break;
        case static::COLUMN_EMAIL:
          $user->email=$value;
          break;
        case static::COLUMN_NAME:
          $user->name=$value;
          break;
        case static::COLUMN_NOTES:
          $user->notes=$value;
          break;
        case static::COLUMN_PHONE:
          $user->phone=$value;
          break;
        case static::COLUMN_QQ:
          $user->qq=$value;
          break;
        case static::COLUMN_REGISTER_DATE:
          $user->register_date=$value;
          break;
        case static::COLUMN_ALIPAY_ID:
          $user->alipay_id=$value;
          break;
        case static::COLUMN_WECHAT_ID:
          $user->wechat_id=$value;
          break;
      }
    }

    return $user;
  }

  private function checkUserError(User $user){
    /**
     * @see UserImporter::validHeader()
     */
    if(!$user->email)
      return _ix('Email address missing','User importer');
    else
      return null;
  }

  private function newUser():User{
    $user=new User;
    // Users will need to create a new password
    $user->password=bcrypt(str_random(10));
    $user->role=User::ROLE_EMPLOYER;
    $user->locale=LOCALE_CN;
    $user->timezone='Asia/Shanghai';

    return $user;
  }

  /**
   * @var PaymentManager
   */
  private $paymentManager;
}
