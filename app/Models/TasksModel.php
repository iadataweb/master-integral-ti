<?php
    class TasksModel extends Mysql
    {
        public function all(): array
        {
            return $this->select_all('SELECT * FROM work_tasks WHERE deleted = 0 ORDER BY id_task DESC');
        }

        public function find(int $id)
        {
            return $this->select('SELECT * FROM work_tasks WHERE id_task = ? AND deleted = 0', [$id]);
        }

        public function save(array $task, int $user, int $id = 0): bool
        {
            $values = [$task['title'], $task['requester'], $task['priority'], $task['status'], $task['due_date'] ?: null, $task['description'], $task['result']];
            if ($id > 0) {
                $values[] = $id;
                return $this->update('UPDATE work_tasks SET title=?, requester=?, priority=?, status=?, due_date=?, description=?, result=? WHERE id_task=? AND deleted=0', $values);
            }
            $values[] = $user;
            return (int)$this->insert('INSERT INTO work_tasks (title,requester,priority,status,due_date,description,result,created_by) VALUES (?,?,?,?,?,?,?,?)', $values) > 0;
        }

        public function remove(int $id): bool
        {
            return $this->update('UPDATE work_tasks SET deleted=1 WHERE id_task=? AND deleted=0', [$id]);
        }
    }
