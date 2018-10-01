
<?php

/**
 * @author Pablo Sánchez
 *
 * SÓLO EXTRAJE LO QUE NECESITABA Y LO PLASME EN ESTE ARCHIVO
 * HICE MODIFICACION MÍNIMAS SOBRE LAS FUNCIONES ORIGINALES.
 *
 * Basado en: General PHP Barcode Generator [Casper Bakker - picqer.com] -> TCPDF Barcode Generator [Nicola Asuni]
 *
 */

 // Copyright (C) 2002-2015 Nicola Asuni - Tecnick.com LTD
 //
 // This file is part of TCPDF software library.
 //
 // TCPDF is free software: you can redistribute it and/or modify it
 // under the terms of the GNU Lesser General Public License as
 // published by the Free Software Foundation, either version 3 of the
 // License, or (at your option) any later version.
 //
 // TCPDF is distributed in the hope that it will be useful, but
 // WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 // See the GNU Lesser General Public License for more details.
 //
 // You should have received a copy of the License
 // along with TCPDF. If not, see
 // <http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT>.
 //
 // See LICENSE.TXT file for more information.



  /**
   * Interleaved 2 of 5 barcodes.
   * Compact numeric code, widely used in industry, air cargo
   * Contains digits (0 to 9) and encodes the data in the width of both bars and spaces.
   *
   * @param $code (string) code to represent.
   * @return array barcode representation.
   * @protected
   */
  function barcode_i25($code)
  {
      $chr['0'] = '11221';
      $chr['1'] = '21112';
      $chr['2'] = '12112';
      $chr['3'] = '22111';
      $chr['4'] = '11212';
      $chr['5'] = '21211';
      $chr['6'] = '12211';
      $chr['7'] = '11122';
      $chr['8'] = '21121';
      $chr['9'] = '12121';
      $chr['A'] = '11';
      $chr['Z'] = '21';

      if ((strlen($code) % 2) != 0) {
          // add leading zero if code-length is odd
          $code = '0' . $code;
      }
      // add start and stop codes
      $code = 'AA' . strtolower($code) . 'ZA';

      $bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
      $k = 0;
      $clen = strlen($code);
      for ($i = 0; $i < $clen; $i = ($i + 2)) {
          $char_bar = $code{$i};
          $char_space = $code{$i + 1};
          if ( ! isset($chr[$char_bar]) || ! isset($chr[$char_space])) {
              //throw new InvalidCharacterException();
              echo "ERROR";
          }
          // create a bar-space sequence
          $seq = '';
          $chrlen = strlen($chr[$char_bar]);
          for ($s = 0; $s < $chrlen; $s++) {
              $seq .= $chr[$char_bar]{$s} . $chr[$char_space]{$s};
          }
          $seqlen = strlen($seq);
          for ($j = 0; $j < $seqlen; ++$j) {
              if (($j % 2) == 0) {
                  $t = true; // bar
              } else {
                  $t = false; // space
              }
              $w = $seq{$j};
              $bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
              $bararray['maxw'] += $w;
              ++$k;
          }
      }

      return convertBarcodeArrayToNewStyle($bararray);
  }


  function convertBarcodeArrayToNewStyle($oldBarcodeArray)
  {
      $newBarcodeArray = [];
      $newBarcodeArray['code'] = $oldBarcodeArray['code'];
      $newBarcodeArray['maxWidth'] = $oldBarcodeArray['maxw'];
      $newBarcodeArray['maxHeight'] = $oldBarcodeArray['maxh'];
      $newBarcodeArray['bars'] = [];
      foreach ($oldBarcodeArray['bcode'] as $oldbar) {
          $newBar = [];
          $newBar['width'] = $oldbar['w'];
          $newBar['height'] = $oldbar['h'];
          $newBar['positionVertical'] = $oldbar['p'];
          $newBar['drawBar'] = $oldbar['t'];
          $newBar['drawSpacing'] = ! $oldbar['t'];

          $newBarcodeArray['bars'][] = $newBar;
      }

      return $newBarcodeArray;
  }

  /**
   * Return an HTML representation of barcode.
   *
   * @param string $code code to print
   * @param int $widthFactor Width of a single bar element in pixels.
   * @param int $totalHeight Height of a single bar element in pixels.
   * @param int|string $color Foreground color for bar elements (background is transparent).
   * @return string HTML code.
   * @public
   */
  function getBarcode($code, $widthFactor = 2, $totalHeight = 30, $color = 'black')
  {
      $barcodeData = barcode_i25($code);

      $html = '<div style="font-size:0;position:relative;width:' . ($barcodeData['maxWidth'] * $widthFactor) . 'px;height:' . ($totalHeight) . 'px;">' . "\n";

      $positionHorizontal = 0;
      foreach ($barcodeData['bars'] as $bar) {
          $barWidth = round(($bar['width'] * $widthFactor), 3);
          $barHeight = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);

          if ($bar['drawBar']) {
              $positionVertical = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3);
              // draw a vertical bar
              $html .= '<div style="background-color:' . $color . ';width:' . $barWidth . 'px;height:' . $barHeight . 'px;position:absolute;left:' . $positionHorizontal . 'px;top:' . $positionVertical . 'px;">&nbsp;</div>' . "\n";
          }

          $positionHorizontal += $barWidth;
      }

      $html .= '</div>' . "\n";

      return $html;
  }




$cb = "0123456789";
$tst = getBarcode($cb);

echo $tst;

?>
