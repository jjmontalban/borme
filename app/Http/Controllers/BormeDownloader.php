<?php

namespace App\Http\Controllers;

use Storage;
use File;

use Illuminate\Http\Request;

class BormeDownloader extends Controller
{
    
    public function downloadBorme(Request $url)
    {
    	/* Descargar el PDF y gurdarlo en ruta temporal */
    	$address = $url->url;
		Storage::put(basename($address) , file_get_contents($address));	
		$rutaTemporal = storage_path('app');

		/* Pasar contenido a cadena de texto */
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile($rutaTemporal . "/" . basename($address));
		$text = $pdf->getText();

		/* Guaradar contenido en un nuevo archivo y eliminar el original */
		$filename = basename($address, ".pdf");
		$ruta = storage_path('public');

		if(file_put_contents( $filename . ".txt" , $text )){
			File::delete($rutaTemporal . "/" . basename($address));
			echo("Archivo guardado en ". $ruta);
			return view('welcome');
			}
		else
			return response()->json(['message' => 'Error al almacenar archivo']);

    }
}

