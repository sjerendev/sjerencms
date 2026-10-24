<?php

it('renders four service columns with one item per line', function () {
    blockPage('service_cards', ['number' => '03', 'label' => 'Tjänster', 'columns' => [
        ['title' => 'Design', 'items' => "UI/UX-design\nDesignsystem"],
        ['title' => 'Utveckling', 'items' => 'Frontend'],
    ]]);

    $this->get('/se')->assertSeeInOrder(['03', 'Tjänster', 'Design', 'UI/UX-design', 'Designsystem', 'Utveckling', 'Frontend']);
});

it('renders the about teaser with its cards', function () {
    blockPage('headline_text', ['number' => '04', 'label' => 'Om mig', 'headline' => 'Först house.', 'text' => 'Jag har byggt.', 'cards' => [['title' => 'Design', 'body' => 'Logotyp.']]]);

    $this->get('/se')->assertSeeInOrder(['04', 'Om mig', 'Först house.', 'Jag har byggt.', 'Design', 'Logotyp.']);
});

it('numbers the questions and shows the note', function () {
    blockPage('faq_accordion', ['number' => '04', 'label' => 'Sex frågor', 'items' => [
        ['question' => 'Vad gör du?', 'answer' => 'Jag designar.', 'note' => 'I samklang.'],
        ['question' => 'Och innan?', 'answer' => 'Ljud.', 'note' => 'Rytm.'],
    ]]);

    $this->get('/se')->assertSeeInOrder(['01', 'Vad gör du?', 'Jag designar.', 'I samklang.', '02', 'Och innan?']);
});
