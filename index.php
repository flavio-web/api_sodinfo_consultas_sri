<?php
require_once("vendor/autoload.php");
require_once("helpers/arrays.helper.php");

$response = '';

try{

    if( isset($_GET['AUTORIZACION']) && !empty($_GET['AUTORIZACION']) ){
        if(strlen($_GET['AUTORIZACION']) === 49){
            $cliente = new nusoap_client("https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl", true);


            $cliente->useHTTPPersistentConnection();
            $cliente->soap_defencoding = 'UTF-8';
            $parametros = array(
                'claveAccesoComprobante'=> $_GET['AUTORIZACION']
            );
            $respuesta = $cliente->call("autorizacionComprobante", $parametros);
           
            if ($cliente->fault) {
               throw new Exception($respuesta);
            } else {
                $err = $cliente->getError();
               
                if ( !$err ) {
                   /*  echo json_encode( mb_convert_encoding($respuesta, 'ISO-8859-1', 'UTF-8') );
                    exit; */
                    if(intval($respuesta['RespuestaAutorizacionComprobante']['numeroComprobantes']) > 0){
                  
                        if($respuesta['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']){
                            $nodo_comprobante = $respuesta['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'];
    
                            //print_r($nodo_comprobante);
                            $autorizacion = $respuesta['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'];
    
                           
                            //function call to convert array to xml
                            $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><autorizacion></autorizacion>');
                            $structureFile = array_to_xml($autorizacion, $xml_user_info);
                            /*echo $NEWXML->asXML();*/
                            //echo $xml_user_info->asXML();
                            //$response['result'] = htmlentities($xml_user_info->asXML());
                            echo htmlentities( strtr($xml_user_info->asXML(), array("\n" => '')) );
                            
                          
                        }else{
                            throw new Exception("El documento no se encuentra autorizado.");
                        }
                    }else{
                        throw new Exception("El número de comprobantes del documento es 0.");
                    }
                }else{
                    throw new Exception($err);
                }
            }
        }
    }
}catch( Exception $e ){
   echo $e->getMessage();
    //echo $response = '';
}


/*ob_clean();*/
//echo json_encode($response);


?>