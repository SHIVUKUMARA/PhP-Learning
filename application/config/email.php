<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONFIGURATION
| -------------------------------------------------------------------
| This file contains the email configuration for CodeIgniter.
| Uses Gmail SMTP in this example.
*/

// Mailtrap SMTP configuration (commented out)
// $config = array(
//     'protocol'  => 'smtp',
//     'smtp_host' => 'sandbox.smtp.mailtrap.io',
//     'smtp_port' =>  587,
//     'smtp_user' => '8156fb8e2a7ef9',
//     'smtp_pass' => '061b98cf8916d1',
//     'mailtype'  => 'html',
//     'charset'   => 'utf-8',
//     'newline'   => "\r\n",
//     'crlf'      => "\r\n",
//     'wordwrap'  => TRUE,
//     'validate'  => TRUE,
//     'smtp_crypto' => 'tls',
//     'priority'  => 1
// );

// Google SMTP configuration
$config = array(
    'protocol'  => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' =>  587,
    'smtp_user' => 'shivukumaraspatil01@gmail.com',
    'smtp_pass' => 'loij cpat ccca ugpy',
    'mailtype'  => 'html',
    'charset'   => 'utf-8',
    'newline'   => "\r\n",
    'crlf'      => "\r\n",
    'wordwrap'  => TRUE,
    'validate'  => TRUE,
    'smtp_crypto' => 'tls',
    'priority'  => 1
);
