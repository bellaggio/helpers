<?php

namespace Usuario\Library;

/*
 * FactoryForm = Cria campos de formulário de forma dinamica e acrescenta valores caso exista
 * v1.0
 * Rafael Silva da Costa
 */

class FactoryForm {

    protected static $_form = array();
    protected static $_typeInput;
    protected static $_optionInput;
    protected static $_valueInput;
    protected static $_classInput;
    protected static $_checklist;

    //função statica para criar formulário
    public static function CreateForm($input, $data = null) {

        foreach ($input['collumns'] as $key => $value) {
            self::$_form[$key] = self::typeInput($key, $value, $data);
        }
        return self::$_form;
    }

    //cria o tipo de input
    private static function typeInput($key, $type, $data = null) {

        self::$_typeInput = "";
        switch ($type['type']) {
            case 'text': {
                    self::$_typeInput = "<div class='row'><div class='span2 form-title-input' >{$type['alias']}:</div>";
                    self::$_typeInput .= "<div class='span2'><input type='text' name='{$key}' placeholder='{$type['alias']}' " . self::classInput(@$type['class']) . " " . self::valueInput($key, $type['type'], $data) . "  /></div></div>";
                    break;
                }
            case 'hidden': {
                    self::$_typeInput = "<input type='hidden' name='{$key}' " . self::valueInput($key, $type['type'], $data) . "  />";
                    break;
                }
            case 'file': {
                    self::$_typeInput = "<div class='row'><div class='span8'>
                        <div style='position:relative;'>
                            <a class='btn' href='javascript:;' style='width: 150px'>
                                {$type['bottomName']}
                                <input type='file' id='fileupload' name='{$key}[]' data-url='{$type['urlPost']}' multiple style=\"position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);opacity:0;background-color:transparent;color:transparent;\"  size='40'  onchange='initAjax.getName($(this));'>
                            </a>
                            &nbsp;
                            <span class='label label-info' id='upload-file-info'>" . self::valueInput($key, $type['type'], $data) . "</span>
                        </div>
                        <br/>
                        <div id='progress'>
                            <div class='bar' style='width: 0%;'></div></div>
                        </div>
                    </div>";
                    break;
                }
            case 'checkbox': {
                    self::$_typeInput = "<div class='row'><div class='span2 form-title-input'>{$type['alias']}:</div>";
                    self::$_typeInput .= "<div class='span1'><input type='checkbox' name='{$key}' " . self::classInput(@$type['class']) . " value=1 " . self::valueInput($key, $type['type'], $data) . " /></div></div>";
                    break;
                }
            case 'checklist': {
                    self::$_typeInput = "<div class='row'><div class='span2 form-title-input'>{$type['alias']}:</div>";
                    self::$_typeInput .= "<div class='span8'><ul class='form-title-input-ul'>" . self::checklist($type, $key) . "</ul></div>";
                    break;
                }
            case 'radio': {
                    self::$_typeInput = "<div class='row'><div class='span2 form-title-input'>{$type['alias']}:</div>";
                    self::$_typeInput .= "<div class='span1'><input type='radio' name='{$key}'  " . self::classInput($type['class']) . " " . self::valueInput($key, $type['type'], $data) . " /></div></div>";
                    break;
                }
            case 'select': {
                    self::$_typeInput = "<div class='row'><div class='span2 form-title-input'>{$type['alias']}:</div>";
                    self::$_typeInput .= "<div class='span2'><select type ='text' name='{$key}' " . self::classInput(@$type['class']) . ">";
                    self::$_typeInput .= self::optionSelect($type, $data);
                    self::$_typeInput .= "</select></div></div>";

                    break;
                }
        }

        return self::$_typeInput;
    }

    //acrescenta uma class como atributo html
    private static function classInput($class = null) {

        self::$_classInput = "";
        if ($class != "") {
            return self::$_classInput = "class='" . $class . "'";
        }
        return self::$_classInput;
    }

    //acrescenta valor aos campos do formulário
    private static function valueInput($key, $type, $value = null) {
        self::$_valueInput = "";
        if (is_object($value)) {
            switch ($type) {
                case 'text': {
                        $info = $value->{'get' . ucfirst($key)}() instanceof \Datetime ? $value->{'get' . ucfirst($key)}()->format('d/m/Y') : $value->{'get' . ucfirst($key)}();

                        self::$_valueInput = "value='" . $info . "'";

                        break;
                    }
                case 'hidden': {
                        self::$_valueInput = "value='" . $value->{'get' . ucfirst($key)}() . "'";
                        break;
                    }
                case 'checkbox' : {
                        if ($value->{'get' . ucfirst($key)}() == 1) {
                            self::$_valueInput = "checked='checked'";
                        }
                        break;
                    }
                case 'file' : {
                        self::$_valueInput = $value->{'get' . ucfirst($key)}();
                        break;
                    }
            }
        }
        return self::$_valueInput;
    }

    //cria a lista de options para select list
    private static function optionSelect($option, $data = null) {

        self::$_optionInput = "";
        foreach ($option['option'] as $value) {
            self::$_optionInput .="<option value='{$value->getId()}'>" . $value->{'get' . ucfirst($option['defaultCollumns'])}() . "</option>";
        }
        return self::$_optionInput;
    }

    private static function checklist($option, $key, $data = null) {
        self::$_checklist = "";
        foreach ($option['option'] as $value) {
            $checked = in_array($value->getId(), $option['data']) ? "checked=checked" : "";
            self::$_checklist .= "<li>" . $value->{'get' . ucfirst($option['defaultCollumns'])}();
            self::$_checklist .= " <input type='checkbox' name='{$key}[]' value='{$value->getId()}' {$checked}  ></li>";
        }
        return self::$_checklist;
    }

}

/* - formato para chamar select no controller
 * @defaultCollumns = texto a ser exibido nos options. obs. verificar entidade
 * $this->form['collumns']['teste']=array('type'=>'select','alias'=>'Noticias','option'=>$options,'defaultCollumns'=>'role');     
 *  */