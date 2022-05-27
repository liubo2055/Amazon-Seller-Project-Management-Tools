<?php

main();

function main():void{
  foreach(glob('storage/framework/messages/*.php') as $file){
    $php=file_get_contents($file);

    if(substr($php,0,5)!=='<?php'){
      update($file,$php);
      printf("%s\n",$file);
    }
  }
}

function update(string $file,string $php):void{
  if(strpos($php,'<template>')!==false){
    preg_match_all('|_ix\(\'.+?\',\'.+?\'\)|',$php,$texts);
    $texts=$texts[0];
    $texts=array_map(function(string $text):string{
      return sprintf('%s;',$text);
    },$texts);

    $php=implode("\n",$texts);
    $php.="\n";
  }

  $php=sprintf("<?php\n\n%s",$php);

  file_put_contents($file,$php);
}
