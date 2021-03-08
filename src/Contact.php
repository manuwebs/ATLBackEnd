<?php
namespace Src;

use Src\Base;

class Contact extends Base
{
    public $firstName;
    public $lastName;
    public $email;
    private $entityName = 'Contact';

    public function __construct($db, $requestMethod, $id)
    {
        $findQuery = "
        SELECT 
        c.id, firstName, lastName, email, cp.phone
    FROM
        contacts c
    LEFT JOIN
        contacts_phones cp
        ON cp.contactId = c.id
            WHERE c.id = :id;
        ";
        parent::__construct($db, $requestMethod, $id, $findQuery);
    }

    public function getAll()
    {
        $query = "
        SELECT 
                c.id, firstName, lastName, email, cp.phone
            FROM
                contacts c
            LEFT JOIN
                contacts_phones cp
                ON cp.contactId = c.id;
        ";
        return $this->_getAll($query);
    }

    public function create()
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);
        if (!$this->validateInput($input)) {
            return $this->badRequestResponse();
        }

        $query = "
            INSERT INTO contacts 
                (firstName, lastName, email)
            VALUES
                (:firstName, :lastName, :email);
        ";

        $query2 = "
            INSERT INTO contacts_phones 
                (contactId, phone)
            VALUES ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                'firstName' => $input['firstName'],
                'lastName'  => $input['lastName'],
                'email' => $input['email'],
            ]);
            $insertedId = $this->db->lastInsertid();

            foreach ($input['phones'] as $record) {
                $values[] = '('.$insertedId.','.$record.')';
            }

            if (!empty($values)) {
                $query2.= implode(', ', $values);
                $statement = $this->db->prepare($query2);
                $statement->execute();
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        
        return $this->prepareResponse('201', ['message' => $this->entityName.' Created', 'data' => $this->find($insertedId)]);
    }

    public function update()
    {
        $result = $this->find($this->id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), true);
        if (! $this->validateInput($input)) {
            return $this->badRequestResponse();
        }

        $statement = "
            UPDATE contacts
            SET 
                firstName = :firstName,
                lastName  = :lastName,
                email = :email
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'id' => $this->id,
                'firstName' => $input['firstName'],
                'lastName'  => $input['lastName'],
                'email' => $input['email'] ?? '',
            ]);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        
        return $this->prepareResponse('200', ['message' => $this->entityName.' Updated', 'data' => $this->find($this->id)]);
    }

    public function delete()
    {
        $result = $this->find($this->id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $query = "
        DELETE FROM contacts_phones
        WHERE contactId = :id;
        DELETE FROM contacts
        WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $this->id]);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        
        return $this->prepareResponse('200', ['message' => $this->entityName.' Deleted']);
    }


    private function validateInput($input)
    {
        if (! isset($input['firstName'])) {
            return false;
        }
        if (! isset($input['phones'])) {
            return false;
        }

        return true;
    }
}
