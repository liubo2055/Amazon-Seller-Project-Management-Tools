export default function(message,context){
  const messages=window.messages||{}
  const key=context+'|'+message

  let translation=messages[key]
  // Allow empty strings
  if(translation===undefined)
    translation=message

  return translation
}
