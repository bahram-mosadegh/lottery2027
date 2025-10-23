<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'مقدار :attribute باید قابل تایید باشد',
    'accepted_if' => 'مقدار :attribute باید قابل تایید باشد وقتی :other برابر است با :value.',
    'active_url' => 'مقدار :attribute باید یک آدرس معتبر باشد',
    'after' => 'مقدار :attribute باید پس از :date باشد',
    'after_or_equal' => 'مقدار :attribute باید برابر یا بیشتر از :date باشد',
    'alpha' => 'مقدار :attribute باید فقط حروف باشد',
    'alpha_dash' => 'مقدار :attribute باید فقط شامل حروف،اعداد و - باشد',
    'alpha_num' => 'مقدار :attribute باید فقط شامل اعداد و حروف باشد',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'فرمت :attribute با فرمت :format همخوانی ندارد.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'آدرس ایمیل معتبر نیست.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => ':attribute انتخاب شده نامعتبر است.',
    'exists' => ':attribute انتخاب شده نامعتبر است.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => ':attribute انتخاب شده نامعتبر است.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute باید حداقل :min باشد.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => ':attribute انتخاب شده نامعتبر است.',
    'not_regex' => 'فرمت فیلد :attribute معتبر نیست.',
    'numeric' => 'مقدار :attribute باید عدد باشد.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'فرمت فیلد :attribute معتبر نیست.',
    'required' => 'فیلد :attribute اجباری است.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'فیلد :attribute اجباری است وقتی :other برابر است با :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'فیلد :attribute اجباری است وقتی فیلد :values دارای مقدار است.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'فیلد :attribute اجباری است وقتی که :values انتخاب نشده است.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ':attribute تکراری است.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'mobile' => 'موبایل',
        'phone' => 'تلفن',
        'currency.*' => 'واحد پول',
        'title' => 'عنوان',
        'type' => 'نوع',
        'status' => 'وضعیت',
        'product' => 'محصول',
        'coupon' => 'کد تخفیف',
        'code' => 'کد',
        'ammount' => 'مقدار',
        'expire_date' => 'تاریخ انقضا',
        'applicant_face' => 'تصویر چهره متقاضی اصلی',
        'applicant_passport' => 'تصویر پاسپورت متقاضی اصلی',
        'spouse_face' => 'تصویر چهره همسر',
        'spouse_passport' => 'تصویر پاسپورت همسر',
        'adult_child_face.*' => 'تصویر چهره فرزند 18 الی 21 سال',
        'adult_child_passport.*' => 'تصویر پاسپورت فرزند 18 الی 21 سال',
        'child_face.*' => 'تصویر چهره فرزند زیر 18 سال',
    ],

];
