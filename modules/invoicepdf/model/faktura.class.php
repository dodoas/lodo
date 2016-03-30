<?php
/**
 * Module: pdf faktura creator.
 * Purpose:
 *
 *  Author: Geir Eliasssen
 *
 * Copyright Lodo 2005 www.lodo.no
*/

//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);

define('FPDF_FONTPATH',dirname(__FILE__).'/font/');
require(dirname(__FILE__).'/fpdf.php');

/**
 * 2 do: G� gjennom alle variabler for plasseringer for om mulig � gj�re dem mer intuitive samt f� bort harkodede posisjoner.
 *
*/

class pdfInvoice
{
    public $showMyFrame = 0;
    public $RegningBetalerHoyde = 232;
    public $RegningLinjeHoyde = 5;
    public $fakturaOverskrifterHoyde = 83;
    public $feltStart = 9;
    public $lineHeight = 4.5;

    public $invoiceFont = 'Helvetica';
    public $invoiceLineFontSize = 9;
    public $invoiceHeadAdressWidth = 85;

    public $invoicePrintGiro = true;
    public $invoiceGiroFont = 'Courier';
    public $invoiceGiroFontSize = 12;
    public $invoiceAddionalTopMargintForTotalSums = 115;

    public $invoiceHeadSenderStart = 14;
    public $invoiceHeadSenderLeftMargin = 9;

    public $invoiceHeadRecipientStart = 40;
    public $invoiceHeadRecipientLeftMargin = 9;
    public $invoiceHeadRecipientUnderline = 94;

    public $invoiceHeadDeliveryStart = 66;
    public $invoiceHeadDeliveryLeftMargin = 9;

    public $invoiceHeadCompanyInfoStart = 20;
    public $invoiceHeadCompanyInfoLeftMargin = 132;
    public $invoiceHeadCompanyBetingelserLeftMargin = 135;
    public $invoiceHeadCompanyInfoWidth = 29;
    public $invoiceHeadCompanyInfoWidth2 = 22;
    public $invoiceHeadCompanyInfoFont = 9;
    public $invoiceHeadlineHeight = 3.5;

    public $invoiceHeaderStart = 115;
    public $invoiceHeaderLeftMargin = 35;
    public $invoiceHeaderWidth = 22;
    public $invoiceHeaderFont = 16;
    public $invoiceHeaderHeight = 5;

    public $invoiceLinesWithGiroInfo = 14;
    public $invoiceLinesWithoutGiroInfo = 39;
    public $invoiceLinesPerSite;

    public $invoiceLineHeadFontSize = 9;
    public $invoiceLineHeadStart = 98;
    public $invoiceLineHeadLeft1 = 14;
    public $invoiceLineHeadLeft2 = 30;
    public $invoiceLineHeadLeft3 = 93;
    public $invoiceLineHeadLeft4 = 109;
    public $invoiceLineHeadLeft5 = 130;
    public $invoiceLineHeadLeft6 = 146;
    public $invoiceLineHeadLeft7 = 176;
    public $invoiceLineHeadLeft8 = 199;
    public $invoiceLineHeadName1 = "Produktnr";
    public $invoiceLineHeadName2 = "Produktnavn";
    public $invoiceLineHeadName3 = "Antall";
    public $invoiceLineHeadName4 = "Enhetspris";
    public $invoiceLineHeadName5 = "MVA %";
    public $invoiceLineHeadName6 = "MVA bel�p";
    public $invoiceLineHeadName7 = "Bel�p u/MVA";
    public $invoiceLineHeadName8 = "";
    public $invoiceLineRefName1 = "produktnr";
    public $invoiceLineRefName2 = "produktnavn";
    public $invoiceLineRefName3 = "antall";
    public $invoiceLineRefName4 = "enhetspris";
    public $invoiceLineRefName5 = "mva";
    public $invoiceLineRefName6 = "mvabelop";
    public $invoiceLineRefName7 = "linjesum";
    public $invoiceLineAlignment1 = "L";
    public $invoiceLineAlignment2 = "L";
    public $invoiceLineAlignment3 = "R";
    public $invoiceLineAlignment4 = "R";
    public $invoiceLineAlignment5 = "R";
    public $invoiceLineAlignment6 = "R";
    public $invoiceLineAlignment7 = "R";

    public $invoiceLineFootStart = 159.5;
    public $invoiceCommentStart = 164;


    public $headerParams = array();

    public $invoiceLineCurrentLine = 0;
/**
 * Constructor for objektet.
 *
 * @return pdfInvoice
 */
    function pdfInvoice()
    {
        $this->pdf=new FPDF('P', 'mm', 'A4');
    }

/**
 * Setter for invoicePrintGiro
 */
    function setPrintGiro($shouldPringGiro)
    {
      $this->invoicePrintGiro = $shouldPringGiro;
      $this->invoiceLinesPerSite = ($this->invoicePrintGiro) ? $this->invoiceLinesWithGiroInfo : $this->invoiceLinesWithoutGiroInfo;
    }

/**
 * Oppretter ny faktura. Det er fult mulig � ha flere fakturaer i samme PDF fil.
 *
 * @param array $params:
 * (Senders adresse.  Hvis land finnes og det er forskjellig fra mottakers navn kommer det med p� fakturaen.)
 * $params["sender"]["name"]
 * $params["sender"]["address1"]
 * $params["sender"]["address2"]
 * $params["sender"]["zip"]
 * $params["sender"]["city"]
 * $params["sender"]["country"]
 *
 * (Mottakers adresse. Hvis land finnes og det er forskjellig fra senders navn kommer det med p� fakturaen.)
 * $params["recipient"]["name"]
 * $params["recipient"]["address1"]
 * $params["recipient"]["address2"]
 * $params["recipient"]["zip"]
 * $params["recipient"]["city"]
 * $params["recipient"]["country"]
 *
 * $params["companyInfo"]["Kontonr"]
 * $params["companyInfo"]["Orgnr"]
 * $params["companyInfo"][<hvilken som helst fakturaparameter som skal st� i toppen>]
 *
 * $params["invoiceData"]["Fakturanr"]
 * $params["invoiceData"]["Kundenr"]
 * $params["invoiceData"]["Fakturadato"]
 * $params["invoiceData"]["Ordredato"]
 * $params["invoiceData"]["Betalingsfrist"]
 * $params["invoiceData"][<hvilken som helst fakturaparameter som skal st� i toppen>]
 *
 * $params["fakturatype"] (Kan v�re Faktura, Kreditnota, Betalingsoppf�lging, Stengingsvarsel)
 * $params["betingelser"] (Tekstfelt som forteller at hvis ikke kunden betaler s� blir det v�rst for ham.)
 *
 */
    function newInvoice($params)
    {
        // Lager ny faktura.
        $this->headerParams = $params;
        $this->newInvoicePage($params);
    }/**
 * Fuksjon som burde v�rt privat. Brukes internt.
 *
 * @param array $params
 */
    function newInvoicePage($params = "")
    {
        if ($params == "")
            $params = $this->headerParams;
        // Lager ny fakturaside.
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(false);
        $this->fakturaHead($params);
        $this->invoiceLineCurrentLine = 0;
    }
/**
 * Burde v�rt privat. Lager headeren til fakturaen. Kalles av newInvoicePage.
 *
 * @param array $params
 */
    function fakturaHead($params)
    {
        // From 0 mm to 83 mm.
        $this->fakturaHeadSender($params);
        $this->fakturaHeadRecipient($params);
        $this->fakturaHeader($params);
        $this->fakturaHeadInvoiceData($params);
        $this->addComment($params);
        $this->addInvoiceLineHeader ($params);
    }
/**
 * Burde v�rt privat. Lager feltet for fakturaens avsender i toppen.
 *
 * @param array $params
 */
    function fakturaHeadSender($params)
    {
        ////Disse linjene er tatt vekk etter endringspec fra geir
        $lineNumber = 0;
        $this->pdf->SetFont($this->invoiceFont,'B',12);
        ////$this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart -1.5);
        ////$this->pdf->Cell($this->invoiceHeadAdressWidth, 6, $this->korriger($params["sender"]["name"]), $this->showMyFrame);
        $compNameArr = $this->splitString($params["sender"]["name"], 100);
        for ($i =0; $i < count($compNameArr); $i++)
        {
            $this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart -1.5 + ($this->lineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadAdressWidth, 6, $this->korriger($compNameArr[$i]), $this->showMyFrame);
            $lineNumber++;
        }
        $this->pdf->SetFont($this->invoiceFont,'',12);
        ////$lineNumber++;
        $this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart + ($this->lineHeight * $lineNumber));
        $this->pdf->Cell($this->invoiceHeadAdressWidth,$this->lineHeight, $this->korriger($params["sender"]["address1"]),$this->showMyFrame);
        $lineNumber++;
        $this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart + ($this->lineHeight * $lineNumber));
        if ($params["sender"]["address2"] != "")
        {
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["sender"]["address2"]),$this->showMyFrame);
            $lineNumber++;
            $this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart + ($this->lineHeight * $lineNumber));
        }
        if ($params["sender"]["zip"] != "")
        {
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["sender"]["zip"]) . " " . $this->korriger($params["sender"]["city"]), $this->showMyFrame);
            $lineNumber++;
            $this->pdf->SetXY($this->invoiceHeadSenderLeftMargin, $this->invoiceHeadSenderStart + ($this->lineHeight * $lineNumber));
        }

        if ($params["sender"]["country"] != "" && $params["sender"]["country"] != $params["recipient"]["country"])
        {
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["sender"]["country"]), $this->showMyFrame);
        }
    }
/**
 * Burde v�rt privat. Lager feltet for fakturaens mottaker i toppen.
 * @param array $params
 */
    function fakturaHeadRecipient($params)
    {
        // Mottaker navn og adresse
        ////Disse linjene er tatt vekk etter endringspec fra geir
        $lineNumber = 0;
        $this->pdf->SetFont($this->invoiceFont,'B',12);
        ////$this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart -1.5);
        ////$this->pdf->Cell($this->invoiceHeadAdressWidth, 6, $this->korriger($params["recipient"]["name"]), $this->showMyFrame);
        $compNameArr = $this->splitString($params["recipient"]["name"], 100);
        for ($i =0; $i < count($compNameArr); $i++)
        {
            $this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart -1.5 + ($this->lineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadAdressWidth, 6, $this->korriger($compNameArr[$i]), $this->showMyFrame);
            $lineNumber++;
        }
        $this->pdf->SetFont($this->invoiceFont,'',12);
        ////$lineNumber++;

        $recipientAddress = $this->splitString($this->korriger($params["recipient"]["address1"]), 100);
        for ($i =0; $i < count($recipientAddress); $i++) {
          $this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart + ($this->lineHeight * $lineNumber));
          $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $recipientAddress[$i], $this->showMyFrame);
          $lineNumber++;
        }

        $this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart + ($this->lineHeight * $lineNumber));
        if ($params["recipient"]["address2"] != "")
        {
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["recipient"]["address2"]),$this->showMyFrame);
            $lineNumber++;
            $this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart + ($this->lineHeight * $lineNumber));
        }
        if ($params["recipient"]["zip"] != "")
        {
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["recipient"]["zip"]) . " " . $this->korriger($params["recipient"]["city"]), $this->showMyFrame);
        }
        if($params["recipient"]["country"] != "" && $params["sender"]["country"] != $params["recipient"]["country"]) {
            $lineNumber++;
            $this->pdf->SetXY($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart + ($this->lineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight, $this->korriger($params["recipient"]["country"]),$this->showMyFrame);
        }

        // $this->pdf->Line($this->invoiceHeadRecipientLeftMargin, $this->invoiceHeadRecipientStart + ($this->lineHeight * ($lineNumber+1)), $this->invoiceHeadRecipientUnderline, $this->invoiceHeadRecipientStart + ($this->lineHeight * ($lineNumber+1)));

        // Delivery address
        $lineNumber = 0;
        if (isset($params["delivery"]["address1"])) {
          $this->pdf->SetFont($this->invoiceFont,'B', 10);
          if (!isset($params['delivery']['name'])) $params['delivery']['name'] = $params['recipient']['name'];
          $deliverTo = $this->splitString($params["delivery"]["name"], 100);

          $this->pdf->SetXY($this->invoiceHeadDeliveryLeftMargin, $this->invoiceHeadDeliveryStart - 3 + ($this->lineHeight * $lineNumber));
          $this->pdf->SetFillColor(240,240,240);
          $this->pdf->Cell($this->invoiceHeadAdressWidth, 4, "Leveringsadresse", $this->showMyFrame, 0, 'L', 1);
          $lineNumber++;

          for ($i =0; $i < count($deliverTo); $i++)
          {
            $this->pdf->SetXY($this->invoiceHeadDeliveryLeftMargin, $this->invoiceHeadDeliveryStart -3.5 + ($this->lineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadAdressWidth, 4, $this->korriger($deliverTo[$i]), $this->showMyFrame);
            $lineNumber++;
          }
          $this->pdf->SetFont($this->invoiceFont,'',10);
          $deliveryAddress = $this->splitString($this->korriger($params["delivery"]["address1"]), 100);
          for ($i =0; $i < count($deliveryAddress); $i++) {
            $this->pdf->SetXY($this->invoiceHeadDeliveryLeftMargin, $this->invoiceHeadDeliveryStart + ($this->lineHeight * $lineNumber) - 4);
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight - 1, $deliveryAddress[$i], $this->showMyFrame);
            $lineNumber++;
          }
          $this->pdf->SetXY($this->invoiceHeadDeliveryLeftMargin, $this->invoiceHeadDeliveryStart + ($this->lineHeight * $lineNumber) - 4.5);
          $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight - 1, $this->korriger($params["delivery"]["zip"]) . " " . $this->korriger($params["delivery"]["city"]), $this->showMyFrame);
          if($params["delivery"]["country"] != $params["sender"]["country"]) {
            $lineNumber++;
            $this->pdf->SetXY($this->invoiceHeadDeliveryLeftMargin, $this->invoiceHeadDeliveryStart + ($this->lineHeight * $lineNumber) - 5);
            $this->pdf->Cell($this->invoiceHeadAdressWidth, $this->lineHeight - 1, $this->korriger($params["delivery"]["country"]),$this->showMyFrame);
          }
        }
    }
/**
 * Burde v�rt privat. Lager feltet til venstre med fakturainfo.
 *
 * @param array $params
 */
    function fakturaHeadInvoiceData($params)
    {
        // Firmainfo overskrifter
        $lineNumber = 0;
        foreach ($params["companyInfo"] as $key => $value)
        {
            $this->pdf->SetFont($this->invoiceFont,'B', $this->invoiceHeadCompanyInfoFont);
            $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth,$this->invoiceHeadlineHeight, $this->korriger($key),$this->showMyFrame);

            $this->pdf->SetFont($this->invoiceFont,'', $this->invoiceHeadCompanyInfoFont);
            $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin + $this->invoiceHeadCompanyInfoWidth, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth2, $this->invoiceHeadlineHeight, $this->korriger($value),$this->showMyFrame);

            $lineNumber++;
        }

        $this->pdf->SetFont($this->invoiceFont,'B', $this->invoiceHeadCompanyInfoFont);
        $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
        $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth,$this->invoiceHeadlineHeight, "Side",$this->showMyFrame);

        $this->pdf->SetFont($this->invoiceFont,'', $this->invoiceHeadCompanyInfoFont);
        $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin + $this->invoiceHeadCompanyInfoWidth, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));

        $this->pdf->AliasNbPages('{totalPages}');
        $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth2, $this->invoiceHeadlineHeight, $this->pdf->PageNo() . " av {totalPages}",$this->showMyFrame);

        $lineNumber++;

        foreach ($params["invoiceData"] as $key => $value)
        {
            $this->pdf->SetFont($this->invoiceFont,'B', $this->invoiceHeadCompanyInfoFont);
            $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth,$this->invoiceHeadlineHeight, $this->korriger($key),$this->showMyFrame);

            $this->pdf->SetFont($this->invoiceFont,'', $this->invoiceHeadCompanyInfoFont);
            $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin + $this->invoiceHeadCompanyInfoWidth, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth2, $this->invoiceHeadlineHeight,
                             $this->korriger($value),$this->showMyFrame);

            $lineNumber++;
        }

        // Betalingsbetingelser
        if ($params["betingelser"]) {
            $this->pdf->SetFont($this->invoiceFont,'B',$this->invoiceLineFontSize - 1);
            $lines = $this->splitString($params["betingelser"], 42);
            $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
            $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth,$this->invoiceHeadlineHeight, $this->korriger("Betalingsbetingelser"), $this->showMyFrame, 0, "L");
            $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize - 1);

            for($i = 0; $i < count($lines); $i++)
            {
                $myTekst = $lines[$i];
                $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize - 1);

                $myLeft = "invoiceLineHeadLeft1";
                $myRight = "invoiceLineHeadLeft8";
                $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin + $this->invoiceHeadCompanyInfoWidth, $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber));
                $this->pdf->Cell($this->invoiceHeadCompanyInfoWidth2, $this->invoiceHeadlineHeight, $this->korriger($myTekst), $this->showMyFrame, 0, "L");
                $lineNumber++;
            }
        }

        if($this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * ($lineNumber + 1)) > $this->invoiceLineHeadStart - ($this->lineHeight * 1.2)) {
            $this->invoiceLineHeadStart = $this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * ($lineNumber)) + ($this->lineHeight * 1.2) + 10;
            $this->invoiceLinesPerSite = 12;
        }

        if($this->invoiceHeadCompanyInfoStart + ($this->invoiceHeadlineHeight * $lineNumber) > 60) {
           $this->invoiceHeadCompanyBetingelserLeftMargin = 80;
        }

    }
/**
 * Brude v�rt privat. Brukes av fakturaHead. Skriver ut teksten faktura e.l. p� toppen av faktura.
 *
 * @param array $params
 */
    function fakturaHeader($params)
    {
        // Teksten Faktura
        $this->pdf->SetFont($this->invoiceFont,'B',$this->invoiceHeaderFont);
        $this->pdf->SetXY($this->invoiceHeadCompanyInfoLeftMargin, $this->invoiceHeadCompanyInfoStart - 5);
        $this->pdf->Cell($this->invoiceHeaderWidth, $this->invoiceHeaderHeight, $this->korriger($params["fakturatype"]),$this->showMyFrame);
        $this->pdf->Line(130, 20, 200, 20);
    }
/**
 * Burde v�rt privat. Kalles av fakturaHead. Skriver ut overskrifter for tabellen med fakturalinjer.
 *
 * @param array $params
 */
    function addInvoiceLineHeader ($params)
    {
        // From 83 mm to 87 mm
        $this->pdf->SetFont($this->invoiceFont,'B',$this->invoiceLineHeadFontSize);
        for($i = 1; $i < 8; $i++)
        {
            $myLeft = "invoiceLineHeadLeft" . $i;
            $myRight = "invoiceLineHeadLeft" . ($i + 1);
            $myText = "invoiceLineHeadName" . $i;
            $myAlignment = "invoiceLineAlignment" . $i;
            $this->pdf->SetXY($this->{$myLeft}, $this->invoiceLineHeadStart - ($this->lineHeight * 1.2));
            $this->pdf->Cell($this->{$myRight} - $this->{$myLeft} - 1, $this->lineHeight, $this->korriger($this->{$myText}) ,$this->showMyFrame, 0, $this->{$myAlignment});
        }
        $correction = 1;
        $this->pdf->SetLineWidth(0.2);
        $this->pdf->Line(14, $this->invoiceLineHeadStart - $correction, 198, $this->invoiceLineHeadStart - $correction);

    }
/**
 * Lager en fakturalinje.
 *
 * @param array $params:
 * $params["produktnr"]
 * $params["produktnavn"]
 * $params["antall"]
 * $params["enhetspris"]
 * $params["mva"]
 * $params["mvabelop"] (kan beregnes hvis den utelates.)
 * $params["linjesum"] (kan beregnes hvis den utelates.)
 *
 */
    function addInvoiceLine ($params)
    {
        // From 88 mm to 174 mm. ( 30 mm is consumed by full vat spec or 14 mm if simple vat spec. )
        // This means that you have available 86 mm or 21 lines or 14 lines if full spec vat og  18 lines if simple spec vat

        if ($params["linjesum"] == "")
            $params["linjesum"] = $params["enhetspris"] * $params["antall"];
        if ($params["mvabelop"] == "")
            $params["mvabelop"] = $params["linjesum"] * $params["mva"] / 100;

        $myLeft = "invoiceLineHeadLeft2";
        $myRight = "invoiceLineHeadLeft3";
        $myText2 = "invoiceLineRefName2";
        $myText = $this->{$myText2};
        $newLines = count($this->splitString($params[$myText], $this->{$myRight} - $this->{$myLeft} + 5));

        $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize);
        if (!($this->invoiceLineCurrentLine < $this->invoiceLinesPerSite))
        {
            // Her tror jeg vi m� legge opp til at det lages ny header igjen.
            $this->sideskift();
        }
        else if (!(($this->invoiceLineCurrentLine + $newLines - 1) < $this->invoiceLinesPerSite))
        {
            // Her tror jeg vi m� legge opp til at det lages ny header igjen.
            $this->sideskift();
        }

        for($i = 1; $i < 8; $i++)
        {
            $myLeft = "invoiceLineHeadLeft" . $i;
            $myRight = "invoiceLineHeadLeft" . ($i + 1);
            $myText2 = "invoiceLineRefName" . $i;
            $myText = $this->{$myText2};
            $myAlignment = "invoiceLineAlignment" . $i;
            $this->pdf->SetXY($this->{$myLeft}, $this->invoiceLineHeadStart + ($this->lineHeight * $this->invoiceLineCurrentLine));
            if ($i == 2) {
                $params[$myText] = $this->korriger($params[$myText]);
                $prodTekstArray = $this->splitString($params[$myText], $this->{$myRight} - $this->{$myLeft} + 5);
                for($j = 0; $j < count($prodTekstArray); $j++)
                {
                    $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize);
                    if ($j == 0)
                        $this->pdf->Cell($this->{$myRight} - $this->{$myLeft} - 1, $this->lineHeight, $prodTekstArray[$j], $this->showMyFrame, 0, $this->{$myAlignment});
                    else {
                        $this->pdf->SetXY($this->{$myLeft}, $this->invoiceLineHeadStart + ($this->lineHeight * ($this->invoiceLineCurrentLine + $j)));
                        $this->pdf->Cell($this->{$myRight} - $this->{$myLeft} - 1, $this->lineHeight, $prodTekstArray[$j], $this->showMyFrame, 0, $this->{$myAlignment});
                    }
                }
            }
            if ($i == 3) {
                $params[$myText] = number_format( $params[$myText], 2, ',', ' ' );
            }
            if ($i == 4) {
                $params[$myText] = number_format( $params[$myText], 2, ',', ' ' );
            }
            if ($i == 5) {
                $params[$myText] = $params[$myText] . "   ";
            }
            if ($i == 6) {
                $params[$myText] = number_format( $params[$myText], 2, ',', ' ' );
            }
            if ($i == 7) {
                //formater bel�p u/mva
                $params[$myText] = number_format( $params[$myText], 2, ',', ' ' );
            }
            if ($i != 2)
            {
                $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize);
                $this->pdf->Cell($this->{$myRight} - $this->{$myLeft} - 1, $this->lineHeight, $params[$myText], $this->showMyFrame, 0, $this->{$myAlignment});
            }
        }
        $this->invoiceLineCurrentLine += (count($prodTekstArray) > 1) ? count($prodTekstArray) : 1;
    }
/**
 * Burde v�rt privat. Kalles av addInvoiceLine og addTextLine og kanskje noen til. Lager ny side for fakturaen.
 *
 */
    function sideskift()
    {
        $correction = 0.2;
        $this->pdf->SetLineWidth(0.2);
        $this->newInvoicePage();
        $this->invoiceLinesPerSite = $this->invoiceLinesWithoutGiroInfo;
    }
/**
 * Legger inn nye tekstlinjer sammem med fakturalinjene. Denne m� kalles hvis det blir mer enn en linje,
 * men kan ogs� brukes konsekvent.
 *
 * @param array $params:
 * $params["tekst"]
 */
    function addLongTextLine($params)
    {
        $lines = $this->splitString($params["tekst"], 180);
        for($i = 0; $i < count($lines); $i++)
        {
            $params["tekst"] = $lines[$i];
            $this->addTextLine($params);
        }
    }
/**
 * Brude kanskje v�re privat, men kan brukes dersom du �nsker � skrive bare en linje blant fakturalinjene.
 * Kalles ogs� av addLongTextLine som da selv kan dele opp i flere linjer om n�dvendig.
 *
 * @param array $params
 * $params["tekst"]
 */
    function addTextLine ($params)
    {
        // From 88 mm to 169 mm. ( 30 mm is consumed by full vat spec or 14 mm if simple vat spec. )
        // This means that you have available 86 mm or 21 lines or 14 lines if full spec vat og  18 lines if simple spec vat

        $this->pdf->SetFont($this->invoiceFont,'',$this->invoiceLineFontSize);
        if (!($this->invoiceLineCurrentLine < $this->invoiceLinesPerSite))
        {
            // Her tror jeg vi m� legge opp til at det lages ny header igjen.
            $this->sideskift();
        }
        $myLeft = "invoiceLineHeadLeft1";
        $myRight = "invoiceLineHeadLeft8";
        $this->pdf->SetXY($this->{$myLeft}, $this->invoiceLineHeadStart + ($this->lineHeight * $this->invoiceLineCurrentLine));
        $this->pdf->Cell($this->{$myRight} - $this->{$myLeft} - 1, $this->lineHeight, $this->korriger($params["tekst"]), $this->showMyFrame, 0, "L");
        $this->invoiceLineCurrentLine++;
    }
/**
 * Legger inn en summeringslinje.
 *
 * @param array $params:
 * $params["totaltumva"] (totalsum ekskl mva)
 * $params["totaltmva"] (mva bel�p)
 * $params["totaltmmva"] (totalsum inkl mva)
 */
    function addSumLine($params)
    {
        if ($params["comment"] == "")
        {
            $this->invoiceLineFootStart += $this->lineHeight;
        }
        if (!$this->invoicePrintGiro) {
          // Add some more height if to the total sums if we don't want to show giro infomation
          $this->invoiceLineFootStart += $this->invoiceAddionalTopMargintForTotalSums;
        }
        $this->pdf->SetXY(14, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, "Totalt u/MVA", $this->showMyFrame, 0, "R");
        $this->pdf->SetXY(44, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, number_format($params["totaltumva"], 2, ",", " "), $this->showMyFrame, 0, "L");
        $this->pdf->SetXY(74, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, "MVA", $this->showMyFrame, 0, "R");
        $this->pdf->SetXY(104, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, number_format($params["totaltmva"], 2, ",", " "), $this->showMyFrame, 0, "L");
        $this->pdf->SetXY(134, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, "Totalt m/MVA", $this->showMyFrame, 0, "R");
        $this->pdf->SetXY(164, $this->invoiceLineFootStart);
        $this->pdf->Cell(30, $this->lineHeight, number_format($params["totaltmmva"], 2, ",", " "), $this->showMyFrame, 0, "L");

        $correction = 0.2;
        $this->pdf->SetLineWidth(0.2);

        // $this->pdf->Line(14, $this->invoiceLineFootStart - $correction, 198, $this->invoiceLineFootStart - $correction);
        // $this->pdf->Line(14, $this->invoiceLineFootStart + $this->lineHeight + $correction, 198, $this->invoiceLineFootStart + $this->lineHeight + $correction);

    }

    #Place the multinline comment in the correct spot
    function addComment($params) {

        #print_r($params);

        if ($params["comment"] != "")
        {
            $commentH = split("\n", $params["comment"]);
            $ystart = $yorg = $this->invoiceLineHeadStart - 15;

            #print_r($commentH);

            foreach($commentH as $comment) {

                if($params["CommentPlacement"] == 'top')
                {
                    #print "ystart: $ystart<br>";

                    $this->pdf->SetXY(14, $ystart);
                    $this->pdf->Cell(180, $this->lineHeight, $comment, $this->showMyFrame, 0, "L");
                    $ystart += $this->lineHeight * 1.2;
                }
                elseif($params["CommentPlacement"] == 'bottom')
                {
                    $this->pdf->SetXY(15, $this->invoiceCommentStart);
                    $this->pdf->Cell(180, $this->lineHeight, "Kommentar: " . $comment, $this->showMyFrame, 0, "L");
                }
            }

            $this->invoiceLineHeadStart = $this->invoiceLineHeadStart + $ystart - $yorg - 10;
        }
    }

#special function to split a long string and return it as an array with correct length
function SplitByLength($string, $chunkLength=1){

    $result = array();
    $strLength = strlen($string);
    $x = 0;

    while($x < ($strLength / $chunkLength)){
        $result[] = substr($string, ($x * $chunkLength), $chunkLength);
        $x++;
    }

    return $result;
}

/**
 * Skriver ut giro delen av fakturaen. Dette gj�res n�r alle fakturalinjene er p� plass, teksten p� plass
 * og summeringslinjen p� plass.
 *
 * @param array $params:
 * $params["sender"]["name"]
 * $params["sender"]["address1"]
 * $params["sender"]["address2"]
 * $params["sender"]["address3"]
 * $params["sender"]["zip"]
 * $params["sender"]["city"]
 * $params["sender"]["country"]
 *
 * $params["recipient"]["name"]
 * $params["recipient"]["address1"]
 * $params["recipient"]["address2"]
 * $params["recipient"]["address3"]
 * $params["recipient"]["zip"]
 * $params["recipient"]["city"]
 * $params["recipient"]["country"]
 *
 * $params["companyInfo"]["Kontonr"]
 *
 * $params["invoiceData"]["Fakturanr"]
 * $params["invoiceData"]["Kundenr"]
 * $params["invoiceData"]["Betalingsfrist"]
 *
 * (Linjene over skal ha v�rt brukt f�r i fakturaheaderen. totaltmmva er brukt i summeringslinjen.
 * Kun kid er ny hvis du har bankkonto som st�tter dette.)
 * $params["totaltmmva"]
 * $params["kid"]
 *
 */
    function fakturaGiro($params)
    {
        if (!$this->invoicePrintGiro)
          return;
        // From 174 mm to 297 mm.
        // Regning: Belop #1
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        $this->pdf->SetXY(80, 185);
        $this->pdf->Cell(33, $this->lineHeight, number_format(round($params["totaltmmva"], 2), 2, ",", " "),$this->showMyFrame, 0, "R");

        // Regning: Kontonr #1
        $this->pdf->SetXY(16, 185);
        $this->pdf->Cell(35, $this->lineHeight, $params["companyInfo"]["Kontonr"], $this->showMyFrame);

        // Betalingsfrist p� regningen
        $this->pdf->SetXY(167, 197);
        $this->pdf->Cell(23,  $this->lineHeight, $params["invoiceData"]["Betalingsfrist"], $this->showMyFrame);
        /* // Betalingsfrist p� regningen */
        /* $this->pdf->SetXY(167, 209); */
        /* $this->pdf->Cell(23,  $this->lineHeight, $params["invoiceData"]["Valuta"], $this->showMyFrame); */

        // Betalingsinfo p� regningen (overskrifter)
        $this->pdf->SetFont($this->invoiceGiroFont,'B',$this->invoiceGiroFontSize);
        $this->pdf->SetXY(16, 205);
        $this->pdf->Cell(25, $this->lineHeight,'Faktura nr.:',$this->showMyFrame);
        $this->pdf->SetXY(16, 205 + $this->lineHeight);
        $this->pdf->Cell(25, $this->lineHeight,'Kunde nr.:',$this->showMyFrame);

        // Betalingsinfo p� regningen
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        $this->pdf->SetXY(48, 205);
        $this->pdf->Cell(25, $this->lineHeight, $params["invoiceData"]["Fakturanr"],$this->showMyFrame, 0, "R");
        $this->pdf->SetXY(48, 205 + $this->lineHeight);
        $this->pdf->Cell(25, $this->lineHeight, $params["invoiceData"]["Kundenr"],$this->showMyFrame, 0, "R");


        // Navn og adresse (betaler) p� regningen
        ////Disse linjene er tatt vekk etter endringspec fra geir
        $this->pdf->SetFont($this->invoiceGiroFont,'B',$this->invoiceGiroFontSize);
        ////$this->pdf->SetXY(16, 231);
        ////$this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["recipient"]["name"]), $this->showMyFrame);
        $line = 0;
        $this->pdf->SetFont($this->invoiceFont,'B',16);
        $compNameArr = $this->splitString($params["recipient"]["name"], 90);
        $this->pdf->SetFont($this->invoiceGiroFont,'B',$this->invoiceGiroFontSize);
        for ($i =0; $i < count($compNameArr); $i++)
        {
            $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($compNameArr[$i]), $this->showMyFrame);
            $line++;
        }
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        ////$line = 1;
        $recipientAddress = $this->splitString($this->korriger($params["recipient"]["address1"]), 90);
        for ($i =0; $i < count($recipientAddress); $i++) {
          $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
          $this->pdf->Cell(85, $this->lineHeight, $recipientAddress[$i], $this->showMyFrame);
          $line++;
        }
        $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
        if ($params["recipient"]["address2"] != "")
        {
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["recipient"]["address2"]), $this->showMyFrame);
            $line++;
            $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
        }
        if ($params["recipient"]["address3"] != "")
        {
            $this->pdf->Cell(85,$this->lineHeight, $this->korriger($params["recipient"]["address3"]), $this->showMyFrame);
            $line++;
            $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
        }

        $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["recipient"]["zip"]) . " " . $this->korriger($params["recipient"]["city"]), $this->showMyFrame);

        if ($params["recipient"]["country"] != ""  && $params["sender"]["country"] != $params["recipient"]["country"]) {
            $line++;
            $this->pdf->SetXY(16, 231 + ($line * $this->lineHeight));
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["recipient"]["country"]), $this->showMyFrame);
        }

        // Navn og adresse (avsender) p� regningen
        ////Disse linjene er tatt vekk etter endringspec fra geir
        $line = 0;
        $this->pdf->SetFont($this->invoiceFont,'B',16);
        $compNameArr = $this->splitString($params["sender"]["name"], 100);
        $this->pdf->SetFont($this->invoiceGiroFont,'B',$this->invoiceGiroFontSize);
        ////$this->pdf->SetXY(115, 231);
        ////$this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["sender"]["name"]),$this->showMyFrame);
        for ($i =0; $i < count($compNameArr); $i++)
        {
            $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($compNameArr[$i]),$this->showMyFrame);
            $line++;
        }
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        ////$line = 1;
        $senderAddress = $this->splitString($this->korriger($params["sender"]["address1"]), 100);
        for ($i =0; $i < count($senderAddress); $i++) {
          $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
          $this->pdf->Cell(85, $this->lineHeight, $senderAddress[$i], $this->showMyFrame);
          $line++;
        }
        ////$this->pdf->SetXY(115, 231 + (2 * $this->lineHeight));
        $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
        if ($params["sender"]["address2"] != "")
        {
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["sender"]["address2"]), $this->showMyFrame);
            $line++;
            $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
        }
        if ($params["sender"]["address3"] != "")
        {
            $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["sender"]["address3"]), $this->showMyFrame);
            $line++;
            $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
        }
        $this->pdf->Cell(85, $this->lineHeight, $this->korriger($params["sender"]["zip"]) . " " . $this->korriger($params["sender"]["city"]),$this->showMyFrame);
        if ($params["sender"]["country"] != "" && $params["sender"]["country"] != $params["recipient"]["country"])
        {
            $line++;
            $this->pdf->SetXY(115, 231 + ($line * $this->lineHeight));
            $this->pdf->Cell(85,$this->lineHeight, $this->korriger($params["sender"]["country"]), $this->showMyFrame);
        }

        // Regning: Belop #1
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        $this->pdf->SetXY(77, 271);
	if ($params["totaltmmva"] > 0) // For å rette på at floor ikke runder nedover ved neover ved negativt tall.
          $this->pdf->Cell(23, $this->lineHeight, number_format(floor($params["totaltmmva"]), 0, "", " "),$this->showMyFrame, 0, "R");
	else
	  $this->pdf->Cell(23, $this->lineHeight, number_format(ceil($params["totaltmmva"]), 0, "", " "),$this->showMyFrame, 0, "R");
        $this->pdf->SetXY(104, 271);
        $this->pdf->Cell(8, $this->lineHeight, $this->ore_format($params["totaltmmva"]), $this->showMyFrame);

        // Regning: Kontonr #1
        $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
        $this->pdf->SetXY(131, 271);
        $this->pdf->Cell(35, $this->lineHeight, $params["companyInfo"]["Kontonr"], $this->showMyFrame);

        // KID, hvis den finnes
        if ($params["kid"] != "")
        {
            $this->pdf->SetFont($this->invoiceGiroFont,'',$this->invoiceGiroFontSize);
            $this->pdf->SetXY(5, 271);
            $this->pdf->Cell(60, $this->lineHeight, $params["kid"], $this->showMyFrame, 0, "R");

	    includelogic("kid/kid");
	    $kidO = new lodo_logic_kid();
            $this->pdf->SetXY(116, 271);
            $this->pdf->Cell(1, $this->lineHeight, $kidO->gen_value_checksum(str_replace(".","",$params["totaltmmva"])) , $this->showMyFrame, 0, "L");
        }

    }
/**
 * printer fakturaen. Tar ingen innput. Det som er viktig n�r det gjelder �
 * printe faktura er at http header ikke er sendt ettersom dette er en PDF fil.
 *
 */
    function printFaktura()
    {
        $this->pdf->Output();
    }
/**
 * Formaterer �rer for bruk p� giro. Kravet er at det skal v�re to siffer
 * selv om det starter med en 0 og slutter med en 0.
 *
 * @param float $amt totalbel�pet; b�de heltallsdel og desimaler
 * @return string tosiffret tall.
 */
    function ore_format($amt)
    {
        list($tull, $tall) = explode(".", sprintf("%.2F", $amt));
        return $tall;
    }
/**
 * Konverterer en dato av formatet
 *
 * @param string $dato mysql format.
 * @return string dato norsk format.
 */
    function norwegianDate($dato)
    {
        list($y, $m, $d) = explode("-", $dato);
        return $d . "." . $m . "." . $y;
    }
/**
 * Deler opp en string ved en gitt lengde og legger hver av dem i en tabell.
 *
 * @param string $str
 * @param int $maxLength
 * @return array of strings
 */
    function splitString($str, $maxLength)
{
    $words = explode(" ", $str);
    // Split extra long words to two separate words
    $split_one_word = false;
    for($i=0;$i<count($words);$i++) {
      $word = $words[$i];
      if (($str_pxl_len = $this->pdf->GetStringWidth($word)) > $maxLength) {
        $split_one_word = true;
        $str_char_count = strlen($word);
        $percent_fits = $maxLength/$str_pxl_len;
        $chars_fit = (int)($percent_fits*$str_char_count) - 3;
        $words[$i] = array(substr($word, 0, $chars_fit), substr($word, $chars_fit+1));
      }
    }
    if ($split_one_word) $words = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($words)), FALSE); // the php way to flatten an array
    $wordCounter = 0;
    $lineCounter = 0;
    $fromWord = 0;
    $retStr = "";
    while($wordCounter < count($words))
    {
        $myStr = "";
        $retStr = "";
        $makeLine = true;
        while ($makeLine)
        {
            $myStr .= $words[$wordCounter];
            if ($this->pdf->GetStringWidth($myStr) > $maxLength || $wordCounter >= count($words))
            {
                $makeLine = false;
            }
            else
            {
                $retStr .= $words[$wordCounter] . " ";
                $wordCounter++;
            }
        }
        $retArr[$lineCounter] = $retStr;
        $lineCounter++;
    }
    return $retArr;
}
function korriger($input)
{
    return $input;
}
}
?>
