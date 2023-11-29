<?php
$adminEmail = \Illuminate\Support\Facades\Cache::get("settings")["admin_email"];
return [
    'feedback' => 'Feedback',
    'dream' => 'A dream',
    'dream_description' => 'The series is open for cooperation. The best option is electronic communication.',
    'new_book_or_translate' => 'A new book or translation',
    'new_book_or_translate_text' => "If you are an author or a translator, if you have an already finished book or translation, then you can send an email to <span> $adminEmail</span>  In the subject section of the letter, indicate 'Cooperation Offer'. Sent a payer attached to the letter",
    'your_offer_book_name' => 'The full name and short description of the book you are recommending.',
    'about_books' => 'Details about the book: have you already translated or want to translate?',
    'sale' => 'Sale',
    'sale_text' => 'For book sales, corporate packages, and bulk book orders, you can email: <span> .</span> In the subject section of the letter, indicate "Cooperation Offer". Sent a payer attached to the letter',
    'work' => 'Work',
    'work_text' => "To work with Newmag publishing house as a translator, editor, proofreader, you can send your CV to <span> $adminEmail</span> In the subject section of the letter, indicate 'CV'.",
    'partnership' => 'Partnership',
    'partnership_text' => "To hold a joint festival, program or competition, to discuss new ideas, you can send your proposal <span> $adminEmail</span> .",
    'sponsorship' => 'Sponsorship',
    'sponsorship_text' => "For commercial cooperation or sponsorship offers with Newmag publishing house, you can send an email to <span> $adminEmail</span> In the subject of the letter, indicate 'Commercial offer'.",
];
