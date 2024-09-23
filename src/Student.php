<?php

class Student
{
    private static $students = [
        ["id" => 1, "name" => "Alice"],
        ["id" => 2, "name" => "Bob"],
        ["id" => 3, "name" => "Charlie"]
    ];

    public static function getAll()
    {
        return self::$students;
    }

    public static function getById($id)
    {
        foreach (self::$students as $student) {
            if ($student['id'] == $id) {
                return $student;
            }
        }
        return null;
    }

    public static function add($name)
    {
        $newId = count(self::$students) + 1;
        $newStudent = ["id" => $newId, "name" => $name];
        self::$students[] = $newStudent;
        return $newStudent;
    }

    public static function update($id, $name)
    {
        foreach (self::$students as &$student) {
            if ($student['id'] == $id) {
                $student['name'] = $name;
                return $student;
            }
        }
        return null;
    }

    public static function delete($id)
    {
        foreach (self::$students as $key => $student) {
            if ($student['id'] == $id) {
                unset(self::$students[$key]);
                return true;
            }
        }
        return false;
    }
}
