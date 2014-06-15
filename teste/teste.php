<script>
var totalRegistro = parseInt($("#totalRegistro").html());
               var cor_1 = '';
               var cor_2 = '##FFF0F5'
               var j = 0;
               for(x = 0; x<totalRegistro;x++){
                   if(j<=1){
                                           //estilo = "bgcolor= ''";         
                                           $(".transferencia tr.linha").attr('bgcolor', cor_1);
                                           j++;
                                   }
                                   else{
                                          // estilo = "bgcolor= '#FFF0F5'";
                                            $(".transferencia tr.linha").attr('bgcolor', cor_2);
                                           if(x%2 == 0){
                                                   j-2;        
                                           }else if(x%2 == 1){
                                                   j=0;        
                                           }
                                   }
               }
</script>

<?php
$j = 0;
for($i = 0; $i<19;$i++){
	if($j<=1){
		echo "cor um  ".$i%2 ."<br>";	
			
		$j++;
	}
	else{
		echo "cor dois ".$i%2 ."<br>";
		if($i%2 == 0){
			$j-2;	
		}
		elseif($i%2 == 1){
			$j=0;	
		}
	}
}
?>