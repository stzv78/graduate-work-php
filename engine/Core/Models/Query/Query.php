<?php

namespace Engine\Core\Models\Query;

/**
 * Class Query
 * @package Engine\Core\Models\Query
 */
class Query
{
    /**
     * @var \Engine\Core\Database\Connection
     */
    private $db;
    /**
     * @var \Engine\Core\Router\Router::class->data
     */
    private $data;

    /**
     * @param $db
     * @param $data
     * @return array
     */
    public function run($db, $data)
    {
        $this->db = $db;
        $this->data = $data;
        $this->idFiltration();
        return $this->getDataTable();
    }

    /**
     * Фильтр id
     */
    private function idFiltration()
    {
        $data = $this->data;

        if (isset($data['id'])) {
            if (is_numeric($data['id'])) {
                $this->data['id'] = $data['id'];
            } else {
                redirect('/');
            }
        }
    }

    /**
     * @return mixed|string
     */
    private function descFiltration()
    {
        $_POST['description'] = isset($_POST['description']) ? filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING) : '';
        return $_POST['description'];
    }

    /**
     * @return array
     */
    public function getDataTable()
    {
        $objDataBase = $this->db;

        $dataUserArr = $objDataBase->query("SELECT id, name FROM user");
        if (!empty($_POST['sort'])) {
            if ($_POST['sort_by'] === 'date_added' || $_POST['sort_by'] === 'is_done' || $_POST['sort_by'] === 'description') {
                $sqlDataTODO = "SELECT t.id, t.user_id, t.assigned_user_id, t.date_added, t.is_done, t.description, u.name
                          FROM task t
                          INNER JOIN user u ON t.assigned_user_id = u.id
                          WHERE user_id LIKE :id
                          ORDER BY " . $_POST['sort_by'];
                $dataTODO = $objDataBase->query($sqlDataTODO, ["id" => $_SESSION['logUser']['id']]);
            }
        } else {
            $sqlDataTODO = "SELECT t.id, t.user_id, t.assigned_user_id, t.date_added, t.is_done, t.description, u.name
                          FROM task t
                          INNER JOIN user u ON t.assigned_user_id = u.id
                          WHERE user_id LIKE :id";
            $dataTODO = $objDataBase->query($sqlDataTODO, ["id" => $_SESSION['logUser']['id']]);
        }

        if (!empty($_POST['sort_2'])) {
            if ($_POST['sort_by_2'] === 'date_added' || $_POST['sort_by_2'] === 'is_done' || $_POST['sort_by_2'] === 'description') {
                $sqlDataTODO_2 = "SELECT t.id, t.user_id, t.assigned_user_id, t.date_added, t.is_done, t.description, u.name
                          FROM task t
                          INNER JOIN user u ON t.assigned_user_id = u.id
                          WHERE assigned_user_id LIKE :id
                          ORDER BY " . $_POST['sort_by_2'];
                $dataTODO_2 = $objDataBase->query($sqlDataTODO_2, ["id" => $_SESSION['logUser']['id']]);
            }
        } else {
            $sqlDataTODO_2 = "SELECT t.id, t.user_id, t.assigned_user_id, t.date_added, t.is_done, t.description, u.name
                          FROM task t
                          INNER JOIN user u ON t.assigned_user_id = u.id
                          WHERE assigned_user_id LIKE :id";
            $dataTODO_2 = $objDataBase->query($sqlDataTODO_2, ["id" => $_SESSION['logUser']['id']]);
        }

        if ($this->data['action'] === 'edit') {
            $queryEdit = $this->db->query("SELECT description FROM task WHERE id = " . $this->data['id']);
            $valueTask = [
                'description' => $queryEdit[0]['description'],
                'button' => 'Изменить',
                'action' => 'save&edit'
            ];
        } else {
            $valueTask = [
                'description' => $this->descFiltration(),
                'button' => 'Добавить',
                'action' => 'create'
            ];
        }

        return [
            'users' => $dataUserArr,
            'table_1' => $dataTODO,
            'table_2' => $dataTODO_2,
            'description' => $valueTask['description'],
            'button' => $valueTask['button'],
            'action' => $valueTask['action']
        ];
    }

    /**
     * Отмечает задачу как выполненую
     */
    public function done()
    {
        $sqlDone = "UPDATE task SET is_done = 1 WHERE id = " . $this->data['id'];
        $this->db->execute($sqlDone);
        redirect('/');
    }

    /**
     * Удаляет задачу
     */
    public function delete()
    {
        $sqlDelete = "DELETE FROM task WHERE id =" . $this->data['id'];
        $this->db->execute($sqlDelete);
        redirect('/');
    }

    /**
     * Создаёт и редактирует задачу
     */
    public function create()
    {
        $description = $this->descFiltration();
        $id = $this->data['id'];
        if ($this->data['action'] === 'save&edit') {
            $sqlSave = "UPDATE task SET description = '" . $description . "' WHERE id = " . $id;
            $this->db->execute($sqlSave);
            redirect('/');
        } else {
            $time = date('Y-m-d H:i:s');
            $sqlSaveValues = "( :user_id, :assigned_user_id, :description, :date_added)";
            $sqlSave = "INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES " . $sqlSaveValues;
            $sqlSaveArr = [
                'user_id' => $_SESSION['logUser']['id'],
                'assigned_user_id' => $_SESSION['logUser']['id'],
                'description' => $description,
                'date_added' => $time
            ];
            $this->db->execute($sqlSave, $sqlSaveArr);
            redirect('/');
        }
    }

    /**
     * Присваевает задачу пользователю
     */
    public function delegation()
    {
        $idUserAndTask = explode('&', $_POST['assigned_user_id']);
        $sqlAssigned = "UPDATE task SET assigned_user_id = :assigned_user_id WHERE id = :id";
        $sqlAssignedArr = [
            'assigned_user_id' => $idUserAndTask[0],
            'id' => $idUserAndTask[1]
        ];
        $this->db->execute($sqlAssigned, $sqlAssignedArr);
        redirect('/');
    }
}