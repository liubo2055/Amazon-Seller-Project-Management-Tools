<?php

return [
  // Not used currently
  'accepted'=>'This field must be accepted.',
  'active_url'=>'The :attribute is not a valid URL.',
  'after'=>'The :attribute must be a date after :date.',
  'after_or_equal'=>'The :attribute must be a date after or equal to :date.',
  'alpha'=>'The :attribute may only contain letters.',
  'alpha_dash'=>'The :attribute may only contain letters, numbers, and dashes.',
  'alpha_num'=>'The :attribute may only contain letters and numbers.',
  'array'=>'The :attribute must be an array.',
  'before'=>'The :attribute must be a date before :date.',
  'before_or_equal'=>'The :attribute must be a date before or equal to :date.',
  'between'=>[
    'numeric'=>'The :attribute must be between :min and :max.',
    'file'=>'The :attribute must be between :min and :max kilobytes.',
    'string'=>'The :attribute must be between :min and :max characters.',
    'array'=>'The :attribute must have between :min and :max items.'
  ],
  'boolean'=>'The :attribute field must be true or false.',
  'confirmed'=>'The confirmation does not match.',
  'date'=>'The :attribute is not a valid date.',
  'date_format'=>'The :attribute does not match the format :format.',
  'different'=>'The :attribute and :other must be different.',
  'digits'=>'The :attribute must be :digits digits.',
  'digits_between'=>'The :attribute must be between :min and :max digits.',
  'dimensions'=>'The :attribute has invalid image dimensions.',
  'exists'=>'The selected :attribute is invalid.',
  'file'=>'The :attribute must be a file.',
  'filled'=>'The :attribute field is required.',
  'image'=>'The :attribute must be an image.',
  'in_array'=>'The :attribute field does not exist in :other.',
  'ip'=>'The :attribute must be a valid IP address.',
  'json'=>'The :attribute must be a valid JSON string.',
  'mimes'=>'The :attribute must be a file of type: :values.',
  'mimetypes'=>'The :attribute must be a file of type: :values.',
  'not_in'=>'The selected :attribute is invalid.',
  'present'=>'The :attribute field must be present.',
  'required_if'=>'This field is required.',
  'required_unless'=>'The :attribute field is required unless :other is in :values.',
  'required_with'=>'The :attribute field is required when :values is present.',
  'required_with_all'=>'The :attribute field is required when :values is present.',
  'required_without'=>'The :attribute field is required when :values is not present.',
  'required_without_all'=>'The :attribute field is required when none of :values are present.',
  'same'=>'The :attribute and :other must match.',
  'size'=>[
    'numeric'=>'The :attribute must be :size.',
    'file'=>'The :attribute must be :size kilobytes.',
    'string'=>'The :attribute must be :size characters.',
    'array'=>'The :attribute must contain :size items.'
  ],
  'timezone'=>'The :attribute must be a valid zone.',
  'uploaded'=>'The :attribute failed to upload.',
  // Used currently
  'distinct'=>'This field has a duplicate value.',
  'email'=>'This field must be a valid email address.',
  'in'=>'This field is invalid.',
  'integer'=>'This field must be an integer.',
  'max'=>[
    'numeric'=>'This field may not be greater than :max.',
    'file'=>'The file may not be greater than :max kilobytes.',
    'string'=>'This field may not be greater than :max characters.',
    'array'=>'This field may not have more than :max items.'
  ],
  'min'=>[
    'numeric'=>'This field must be at least :min.',
    'file'=>'The file must be at least :min kilobytes.',
    'string'=>'This field must be at least :min characters.',
    'array'=>'This field must have at least :min items.'
  ],
  'numeric'=>'This field must be a number.',
  'regex'=>'Theis field format is invalid.',
  'required'=>'This field is required.',
  'string'=>'This field must be a string.',
  'unique'=>'The value has already been taken.',
  'url'=>'This field must be a valid URL.',
  'custom'=>[
    'email'=>[
      'unique'=>'This email is already in use by another account.'
    ],
    'code'=>[
      'unique'=>'This ID is already in use by another to-do.',
      'regex'=>'The to-do ID must contain letters, digits, and dashes, and must not use the employer to-do ID format.'
    ]
  ]
];
