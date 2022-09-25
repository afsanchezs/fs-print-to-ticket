<?php
require_once 'plugins/facturacion_base/model/core/terminal_caja.php';

class terminal_caja extends FacturaScripts\model\terminal_caja
{
   /**
    * A partir de una factura añade un ticket a la cola de impresión de este terminal.
    * @param \factura_cliente $factura
    * @param \empresa $empresa
    * @param type $imprimir_descripciones
    * @param type $imprimir_observaciones
    */
   public function imprimir_ticket(&$factura, &$empresa, $imprimir_descripciones = TRUE, $imprimir_observaciones = FALSE)
   {
      $this->add_linea("\n");
      //$this->anchopapel = 40;
      $medio = $this->anchopapel / 2.5;
      $this->add_linea_big( $this->center_text("NOMBRE COMERCIAL", 20)."\n");
      /*
      if($empresa->lema != '')
      {
         $this->add_linea( $this->center_text( $this->sanitize($empresa->lema) ) . "\n\n");
      }
      else
         $this->add_linea("\n");
      */
	   $this->add_linea( $this->center_text("Razon Social")."\n");
      $this->add_linea( $this->center_text("NIT. 123456789-1")."\n");
      $this->add_linea( $this->center_text("No responsable de IVA")."\n");
      $this->add_linea( $this->center_text("Direccion")."\n");
      $this->add_linea( $this->center_text("Ciudad, Departamento")."\n");
	  $this->add_linea( $this->center_text("Telefono - 12345678")."\n");
      $this->add_linea( $this->center_text("Formulario DIAN 123456789143587")."\n");
      $this->add_linea( $this->center_text("Fecha 2020/01/01")."\n");
      $this->add_linea( $this->center_text("Vigencia X meses - Num. Aut.")."\n");
      $this->add_linea( $this->center_text("Prefijo PRE del 1 al 10000")."\n");	  
	  
	  
	  
      /*$this->add_linea(
              $this->center_text( $this->sanitize($empresa->direccion)." - ".$this->sanitize($empresa->ciudad) )."\n"
      );
      $this->add_linea( $this->center_text("NIT: ".$empresa->cifnif) );*/
      //$this->add_linea("\n\n");
      
      /*if($empresa->horario != '')
      {
         $this->add_linea( $this->center_text( $this->sanitize($empresa->horario) ) . "\n\n");
      }
      */
      $linea = "\n"."Factura: PRE" . $factura->codigo . "\n";
      $linea .= "Fecha: " .$factura->fecha. "\n";
      $linea .= "Hora: " . Date('H:i', strtotime($factura->hora)) . "\n";
      $this->add_linea($linea);
      $this->add_linea("Cliente: " . $this->sanitize($factura->nombrecliente) . "\n");
      $this->add_linea("CC/NIT: " . $factura->cifnif . "\n\n");
      
      if($imprimir_observaciones)
      {
         $this->add_linea('Observaciones: ' . $this->sanitize($factura->observaciones) . "\n\n");
      }
      
      $width = $this->anchopapel - 15;
      $this->add_linea(
              sprintf("%3s", "Ud.")." ".
              sprintf("%-".$width."s", "Articulo")." ".
              sprintf("%10s", "TOTAL")."\n"
      );
      $this->add_linea(
              sprintf("%3s", "---")." ".
              sprintf("%-".$width."s", substr("--------------------------------------------------------", 0, $width-1))." ".
              sprintf("%10s", "----------")."\n"
      );
      foreach($factura->get_lineas() as $col)
      {
         if($imprimir_descripciones)
         {
            $linea = sprintf("%3s", $col->cantidad)." ".sprintf("%-".$width."s",
                    substr($this->sanitize($col->descripcion), 0, $width-1))." ".
                    sprintf("%10s", $this->show_numero($col->total_iva()))."\n";
         }
         else
         {
            $linea = sprintf("%3s", $col->cantidad)." ".sprintf("%-".$width."s", $this->sanitize($col->descripcion))
                    ." ".sprintf("%10s", $this->show_numero($col->total_iva()))."\n";
         }
         
         $this->add_linea($linea);
      }
      
      $lineaiguales = '';
      for($i = 0; $i < $this->anchopapel; $i++)
      {
         $lineaiguales .= '=';
      }
      $this->add_linea($lineaiguales."\n");
      $this->add_linea(
              'TOTAL A PAGAR: '.sprintf("%".($this->anchopapel-15)."s", $this->show_precio($factura->total, $factura->coddivisa))."\n"
      );
	  
      $this->add_linea($lineaiguales."\n");
      
      /// imprimimos los impuestos desglosados
	  $this->add_linea(
              'TIPO   BASE    IMPUESTO '.
              sprintf('%'.($this->anchopapel-26).'s', 'TOTAL').
              "\n"
      );
      foreach($factura->get_lineas_iva() as $imp)
      {
         $this->add_linea(
                 sprintf("%-6s", $imp->iva.'%').' '.
                 sprintf("%-7s", $this->show_numero($imp->neto)).' '.
                 sprintf("%-6s", $this->show_numero($imp->totaliva)).' '.
                 sprintf('%'.($this->anchopapel-22).'s', $this->show_numero($imp->totallinea)).
                 "\n"
         );
      }
	  
      $lineaiguales .= "\n\n\n\n\n\n\n\n";
      $this->add_linea($lineaiguales);

      $this->cortar_papel();
   }
}