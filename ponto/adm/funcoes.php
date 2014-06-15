<?php

#############
function show_sel_list($var_select,$var_values_array,$select_item,$array_type)
{
#print "xxxx$select_item<br>";
#array_type=0: a=array("0","1",...)  array_type=1: a=("0"=>"jan", "1"=>"fev",...)

print <<<EOF
<select name="$var_select">
EOF;
##<select name="var_select"  editable onChange="this.form.submit();">
reset($var_values_array);
while (current($var_values_array) !== false) {
     $var_select_crt = key($var_values_array);  #var_select_crt é o indice do array
     $escolha="";
     if($array_type == 0)
     {
        if($var_values_array[$var_select_crt] == $select_item){ $escolha="selected"; }
        print <<<EOF

<option value="$var_values_array[$var_select_crt]"$escolha>
        $var_values_array[$var_select_crt] </option>
EOF;

     }
     else
     {
        if($select_item == $var_select_crt){ $escolha=" selected"; }
        print <<<EOF

<option value="$var_select_crt"$escolha> $var_values_array[$var_select_crt]
</option>
EOF;

     }
     next($var_values_array);
}
print <<<EOF

</select>
EOF;

}

?>
