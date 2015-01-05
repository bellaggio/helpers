<?php

namespace Usuario\Library;

class FactoryGrid {

    protected static $_headerString;
    protected static $_headerGrid;
    protected static $_grid;
    protected static $_bodyGrid;
    protected static $_bodyString;
    protected static $_javascript;
    protected static $_action;
    protected static $_option;

    public static function CreateGrid($header, $data) {
        self::$_grid = "<table id ='grid' class ='display' cellspacing ='0' width ='100%'>";
        self::$_grid .= self::headerGridCreate($header['alias']);
        self::$_grid .= self::bodyGridcreate($data, $header);
        self::$_grid .= "</table>";
        self::$_grid .= self::javascript();
        echo self::$_grid;
    }

    private static function headerGridCreate($header) {
        foreach ($header as $title) {
            self::$_headerString .= "<th>" . $title . "</th>";
        }
        self::$_headerGrid = "<thead>
               <tr>" .
                self::$_headerString
                .
                "</tr>
            </thead>

            <tfoot>
                <tr>" .
                self::$_headerString
                .
                "</tr>
            </tfoot>";

        return self::$_headerGrid;
    }

    private static function bodyGridcreate($data, $header) {

        foreach ($data as $row) {

            self::$_bodyString .= " <tr>";
            foreach ($header['collumns'] as $key => $gets) {
                if (is_array($gets)) {
                    self::$_bodyString .= "<td>" . self::optionFactory($key, $gets, $row) . "</td>";
                } else {
                    self::$_bodyString .= "<td>" . $row->{'get' . ucfirst($gets)}() . "</td>";
                }
            }
            if (isset($header['action'])) {

                self::$_bodyString .="<td>";
                self::$_bodyString .= self::ActionCreated($header['action'],$row->getId());
                self::$_bodyString .= "</td>";
            }
            self::$_bodyString .= "</tr>";
        }
        self::$_bodyGrid = "<tbody>" .
                self::$_bodyString
                . "</tbody>";
        return self::$_bodyGrid;
    }

    private static function ActionCreated($action,$id) {
        self::$_action = "";
        foreach ($action as $act) {
            switch ($act) {
                case 'edit': {
                        self::$_action .= " <img src='/img/edit_img.png' style='cursor:pointer' onclick='initAjax.editable({$id})' />";
                        break;
                    }
                case 'delete': {
                        self::$_action .= " <img src='/img/Delete_Icon.png' />";
                        break;
                    }
            }
        }

        return self::$_action;
    }

    private static function optionFactory($collumn, $option, $data) {
        self::$_option = "";

        switch ($option['option']) {
            case 'active': {
                    if ($data->{'get' . ucfirst($collumn)}() == 1) {
                        self::$_option .= " <img src='/img/open.png' />";
                    } else {
                        self::$_option .= " <img src='/img/close.png' />";
                    }
                    break;
                }
            case 'date': {
                    self::$_option .= $data->{'get' . ucfirst($collumn)}()->format('Y-m-d');
                    break;
                }
            case 'img': {
                    $folder = isset($option['folder']) ? $option['folder'] : "";
                    self::$_option .= "<img src='/img/".$folder.$data->{'get' . ucfirst($collumn)}()."' width='20' height='20'/>";
                    break;
                }
        }
        return self::$_option;
    }

    private static function javascript() {
        self::$_javascript = "
            <script>
                $(document).ready(function() {
                     Grid.simple();
                });
            </script>";
        return self::$_javascript;
    }

}
