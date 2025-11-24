<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session session
 * @property CI_Input input
 * @property CI_Email email
 */

class Email extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
    }

    public function index()
    {
        $this->load->view('email');
    }

    public function send_mail()
    {
        $to_email = $this->input->post('email', true);

        if (!$to_email) {
            $this->session->set_flashdata("email_sent", "Please provide an email address.");
            redirect('email');
        }

        $this->load->library('email');

        $this->email->from('shivukumaraspatil01@gmail.com', 'Admin LTE');
        $this->email->to($to_email);
        $this->email->subject('Welcome to Our Platform!');

        $html_message = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Welcome Email</title>
        </head>
        <body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                            <tr>
                                <td style="background-color: #007bff; color: #ffffff; text-align: center; padding: 30px; font-size: 24px; font-weight: bold;">
                                    Welcome to Our Platform
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 30px; color: #333333; font-size: 16px; line-height: 1.6;">
                                    <p>Hi there,</p>
                                    <p>Thank you for joining our platform! We are thrilled to have you onboard.</p>
                                    <p>Click the button below to verify your email and get started:</p>
                                    <p style="text-align: center;">
                                        <a href="https://example.com/verify" style="background-color: #28a745; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; display: inline-block; font-weight: bold;">Verify Email</a>
                                    </p>
                                    <p>If you did not create this account, please ignore this email.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f4f4f4; color: #777777; text-align: center; padding: 20px; font-size: 12px;">
                                    &copy; ' . date('Y') . ' Your Company Name. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        // Set the email content
        $this->email->message($html_message);

        // Send email
        if ($this->email->send()) {
            $this->session->set_flashdata("email_sent", "✅ Email sent successfully to $to_email");
        } else {
            $this->session->set_flashdata("email_sent", "❌ Error in sending Email.<br>" . $this->email->print_debugger());
        }

        redirect('email'); // redirect back to form
    }
}
