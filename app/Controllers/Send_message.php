<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Send_message extends Controller
{
    // Display contact form
    public function index()
    {
        return view('contact_form');
    }

    // Handle form submission
    public function send()
    {
        $name    = $this->request->getPost('name');
        $email   = $this->request->getPost('email');
        $message = $this->request->getPost('message');

        //regie.catedral@deped.gov.ph
        $to = "jigger.sebarios@lsu.edu.ph, jiggersebarios@gmail.com";
        $subject = "New Message from Archiving System Contact Form";

        $body = "Name: $name\n";
        $body .= "Email: $email\n";
        $body .= "Message:\n$message\n";

        $emailService = \Config\Services::email();
        $emailService->setTo(explode(", ", $to));
        $emailService->setFrom($email, $name);
        $emailService->setSubject($subject);
        $emailService->setMessage($body);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Message sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }
    }
}
