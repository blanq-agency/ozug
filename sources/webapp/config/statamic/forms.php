<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Forms Path
    |--------------------------------------------------------------------------
    |
    | Where your form YAML files are stored.
    |
    */

    'forms' => base_path('data/content/forms'),

    /*
    |--------------------------------------------------------------------------
    | Email View Folder
    |--------------------------------------------------------------------------
    |
    | The folder under resources/views where your email templates are found.
    |
    */

    'email_view_folder' => null,

    /*
    |--------------------------------------------------------------------------
    | Send Email Job
    |--------------------------------------------------------------------------
    |
    | The class name of the job that will be used to send an email.
    |
    */

    'send_email_job' => \Statamic\Forms\SendEmail::class,

];
