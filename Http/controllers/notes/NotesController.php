<?php

namespace Http\controllers\notes;

use Core\App;
use Core\Database;
use Core\Validator;

class NotesController
{
    public function manageMethod($uri, $method){
        if($method === 'GET'){
            if ($uri == "/notes"){
                $this->index();
                return;
            } elseif ($uri == "/note"){
                $this->show();
                return;
            } elseif ($uri == "/notes/create"){
                $this->create();
                return;
            }

            $this->edit();
            return;
        }

        if($method === 'POST'){
            $this->store();
        }

        if($method === 'PATCH'){
            $this->update();
        }

        if($method === 'DELETE'){
            $this->destroy();
        }
    }

    public function create()
    {
        view("notes/create.view.php", [
            'heading' => 'Create Note',
            'errors' => []
        ]);
    }

    public function destroy()
    {
        $db = App::resolve(Database::class);

        $currentUserId = 1;

        $note = $db->query('select * from notes where id = :id', [
            'id' => $_POST['id']
        ])->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        $db->query('delete from notes where id = :id', [
            'id' => $_POST['id']
        ]);

        header('location: /notes');
        exit();
    }

    public function edit()
    {
        $db = App::resolve(Database::class);

        $currentUserId = 1;

        $note = $db->query('select * from notes where id = :id', [
            'id' => $_GET['id']
        ])->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        view("notes/edit.view.php", [
            'heading' => 'Edit Note',
            'errors' => [],
            'note' => $note
        ]);
    }

    public function index()
    {
        $db = App::resolve(Database::class);
        $notes = $db->query('select * from notes where user_id = 1')->get();

        view("notes/index.view.php", [
            'heading' => 'My Notes',
            'notes' => $notes
        ]);
    }

    public function show()
    {
        $db = App::resolve(Database::class);

        $currentUserId = 1;

        $note = $db->query('select * from notes where id = :id', [
            'id' => $_GET['id']
        ])->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        view("notes/show.view.php", [
            'heading' => 'Note',
            'note' => $note
        ]);
    }

    public function store()
    {
        $db = App::resolve(Database::class);
        $errors = [];

        if (!Validator::string($_POST['body'], 1, 1000)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

        if (!empty($errors)) {
            return view("notes/create.view.php", [
                'heading' => 'Create Note',
                'errors' => $errors
            ]);
        }

        $db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body' => $_POST['body'],
            'user_id' => 1
        ]);

        header('location: /notes');
        die();
    }

    public function update()
    {
        $db = App::resolve(Database::class);

        $currentUserId = 1;

    // find the corresponding note
        $note = $db->query('select * from notes where id = :id', [
            'id' => $_POST['id']
        ])->findOrFail();

    // authorize that the current user can edit the note
        authorize($note['user_id'] === $currentUserId);

    // validate the form
        $errors = [];

        if (!Validator::string($_POST['body'], 1, 10)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

    // if no validation errors, update the record in the notes database table.
        if (count($errors)) {
            return view('notes/edit.view.php', [
                'heading' => 'Edit Note',
                'errors' => $errors,
                'note' => $note
            ]);
        }

        $db->query('update notes set body = :body where id = :id', [
            'id' => $_POST['id'],
            'body' => $_POST['body']
        ]);

    // redirect the user
        header('location: /notes');
        die();
    }

}