<?php

namespace tcc\V1\Rest\Checkin;

use Zend\Db\Adapter\Driver\Sqlsrv\Connection;
use Zend\Json\Json;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class CheckinResource extends AbstractResourceListener
{
    public $totally = 0;
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        //objeto organizacao

        if ($params['type'] == 'checkout') $this->checkoutEvent($params);

        if ($params['type'] == 'checkin') {
            $mysqli = new \mysqli("localhost", "root", "", "mydb");

            $query = 'select p.* from card c 
              inner join person p on c.person_id = p.id
              inner join ticket t on t.person_id = p.id 
              where t.event_id = ' . $params["idEvent"] . ' and c.number = ' . $params["numberCard"];

            /* retrieve all rows from myCity */
            $checkTicket = 0;
            if ($result = $mysqli->query($query)) {
                while ($row = $result->fetch_row()) {

                    $contact = $this->searchContact($row[0]);
                    $document = $this->searchDocument($row[0]);
                    $email = $this->searchEmailUser($row[0]);

                    //printf("%s (%s,%s)\n", $row[0], $row[1], $row[2]);
                    $return['result']['name'] = $row[1];
                    $return['result']['picture'] = $row[2];
                    $return['result']['gender'] = $row[3];
                    $return['result']['years'] = $this->calcYearUser($row[7]);
                    $return['result']['contact'] = $contact;
                    $return['result']['document'] = $document;
                    $return['result']['email'] = $email;
                    $checkTicket = 1;
                }
                $return['checkTicket'] = $checkTicket;
                /* free result set */

                $result->close();
            }


            $mysqli->close();

            echo json_encode($return);

            die();

        }

        //return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */

    public function calcYearUser($date)
    {

        $date = new \DateTime($date); // data de nascimento
        $interval = $date->diff(new \DateTime()); // data definida
        return $interval->format('%Y');

    }

    public function checkoutEvent($params)
    {

        $mysqli = new \mysqli("localhost", "root", "", "mydb");

        $query = 'select p.* from card c 
              inner join person p on c.person_id = p.id
              inner join ticket t on t.person_id = p.id 
              where t.event_id = ' . $params["idEvent"] . ' and c.number = ' . $params["numberCard"];

        /* retrieve all rows from myCity */
        $checkTicket = 0;
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_row()) {

                $products = $this->searchProducts($row[0], $params['idEvent']);

                //printf("%s (%s,%s)\n", $row[0], $row[1], $row[2]);
                $return['result']['name'] = $row[1];
                $return['result']['picture'] = $row[2];
                $return['result']['products'] = $products;
                $return['result']['totally'] = $this->totally ;

                $checkTicket = 1;
            }
            $return['checkTicket'] = $checkTicket;
            /* free result set */

            $result->close();
        }


        $mysqli->close();

        echo json_encode($return);

        die();

    }

    public function searchProducts($idPerson, $idEvent)
    {
        $mysqli = new \mysqli("localhost", "root", "", "mydb");

        $sql = 'SELECT p.name as productName, pt.product_type as productTypeName,
                p.sale_value_unity as productValueUnity, count(p.id) as qtdProduct
                from sale s
                right join sale_has_product h
                on s.id = h.sale_id 
                inner join product p
                on p.id = h.product_id
                inner join product_type pt
                  on pt.id = p.product_type_id
                where s.person_id = '.$idPerson.'
                  and s.event_id = '.$idEvent.'
                group by p.id';


        $content = [];

        if ($result = $mysqli->query($sql)) {
            $a = 0;
            $sumProducts = 0;
            while ($row = $result->fetch_row()) {
                $content[$a]['productName'] = $row[0];
                $content[$a]['product_type'] = $row[1];
                $content[$a]['productValueUnity'] = $row[2];
                $content[$a]['qtdProduct'] = $row[3];
                $sumProducts += ($row[3]* $row[2]);
                $a++;
            }

            /* free result set */

            $this->totally = $sumProducts;
            $result->close();

        }
        return $content;


    }

    public function searchContact($id)
    {
        $mysqli = new \mysqli("localhost", "root", "", "mydb");

        $query = 'select * from contact c 
              where c.person_id = ' . $id;

        $contact = '';
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_row()) {
                $contact = $row[1];
            }
        }

        $mysqli->close();

        return $contact;

    }

    public function searchEmailUser($id)
    {
        $mysqli = new \mysqli("localhost", "root", "", "mydb");

        $query = 'select username from user u 
              where u.person_id = ' . $id;

        $user = '';
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_row()) {
                $user = $row[0];
            }
        }

        $mysqli->close();

        return $user;

    }

    public function searchDocument($id)
    {
        $mysqli = new \mysqli("localhost", "root", "", "mydb");

        $query = 'select * from document d 
              where d.person_id = ' . $id;

        $document = [];
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_row()) {
                $document[$row[3]] = $row[1];
            }
        }

        $mysqli->close();

        return $document;

    }

    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Patch (partial in-place update) a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patchList($data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for collections');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
