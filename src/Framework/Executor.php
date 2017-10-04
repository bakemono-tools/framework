<?php

namespace framework;


class Executor
{
    public function execute(array $actions, array $params = []) : array
    {
        $tmpArray = [];

        foreach ($actions as $var => $action) {
            $actionPointer = explode(':', $action);

            $actionPointer[1] = $this->parseActionName($actionPointer[1], false);
            $actionPointer[2] = $this->parseActionName($actionPointer[2], true);

            $actionPointer[1] = "module\\" . strtolower($actionPointer[0]) . '\\action\\' . $actionPointer[1] . 'Action';

            $tmpArray[$var] = call_user_func($actionPointer[1] . '::' . $actionPointer[2], $params);
        }

        return $tmpArray;
    }

    /**
     * Transforme une chaine de caractère : hello_world_title => helloWorldTitle
     *
     * @param  string $action
     * @param  bool   $firstLetterLowercase
     * @return string
     */
    public function parseActionName(string $action, bool $firstLetterLowercase) : string
    {

        $response = "";
        $cpt = 0;

        $tmpArray = explode('_', $action);

        foreach ($tmpArray as $item) {

            $cpt++;

            if ($cpt < 2 && $firstLetterLowercase) {
                $response .= $item;
            } else {
                $response .= ucfirst($item);
            }
        }

        return $response;
    }
}