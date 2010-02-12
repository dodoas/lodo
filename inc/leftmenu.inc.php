<div class="leftmenu">
  <h2 class="groupheader">Kasse - K</h2>
  <div class="group">
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=cash_in&voucher_AccountPlanID=<? print $setup['kasseinn']; ?>" class="group_sale">Inn</a> 
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=cash_out&voucher_AccountPlanID=<? print $setup['kasseut']; ?>" class="group_buy">Ut</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list">Ukeomsetning</a>
  </div>
  <h2 class="groupheader">Bank - B</h2>
  <div class="group">
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=bank_in&voucher_AccountPlanID=<? print $setup['bankinn']; ?>" class="group_sale">Inn</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=bank_out&voucher_AccountPlanID=<? print $setup['bankut']; ?>" class="group_buy">Ut</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=bank.list" class="group">Bankutskrift</a>
  </div>
  <h2 class="groupheader">Kj&oslash;p - U / L&oslash;nn - L</h2>
  <div class="group">
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buycash_out&voucher_AccountPlanID=<? print $setup['buycashut']; ?>"                       class="group_buy">Faktura kontant</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buycredit_out&voucher_AccountPlanID=<? print $setup['buycreditreskontro']; ?>"            class="group_buy">Faktura kredit</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buynotacash_out&voucher_AccountPlanID=<? print $setup['buynotacashinn']; ?>"              class="group_buy">Kreditnota kontant</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buynotacredit_out&voucher_AccountPlanID=<? print $setup['buynotacreditreskontro']; ?>"    class="group_buy">Kreditnota kredit</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=salary.list">L&oslash;nnsslipp</a>
  </div>
  <h2 class="groupheader">Salg - S / Faktura</h2>
  <div class="group">
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salecash_in&voucher_AccountPlanID=<? print $setup['salecashut']; ?>"                      class="group_sale">Faktura kontant</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salecredit_in&voucher_AccountPlanID=<? print $setup['salecreditreskontro']; ?>"           class="group_sale">Faktura kredit</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salenotacash_in&voucher_AccountPlanID=<? print $setup['salenotacashut']; ?>"              class="group_sale">Kreditnota kontant</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salenotacredit_in&voucher_AccountPlanID=<? print $setup['salenotacreditreskontro']; ?>"   class="group_sale">Kreditnota kredit</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=invoice.list">Lage faktura</a>
    <a href="<? print $_lib['sess']->dispatch ?>t=borettslag.multifaktura">Husleie faktura</a>
  </div>

  <h2 class="groupheader">Rapporter / oppsett</h2>
  <div class="group">
  <a href="<? print $_lib['sess']->dispatch ?>t=report.list">Rapporter</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=lodo.main">Oppsett</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=timereg.index">Timereg</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=altinn.index">AltInn</a>
  <? if($_sess->get_person('AccessLevel') > 3) { ?>
  <a href="<? print $_lib['sess']->dispatch ?>t=install.list">Installer</a>
  <? } ?>
  </div>
<br />
  <h2 class="groupheader">Annet</h2>
  <div class="group">
  <a href="<? print $_lib['sess']->dispatch ?>t=filarkiv.index">Dokumentarkiv</a>
  <? if($_sess->get_person('AccessLevel') >= 3) { ?>
  <a href="<? print $_lib['sess']->dispatch ?>t=aarsoppgjoer.index" target="_new">&Aring;rsoppgj&oslash;r</a>
  <? } ?>
  </div>
<br />

<!--  <a href="http://www.empatix.com/"><img src="/img/poweredbyempatix.png" width="145" height="46"></a> -->
<!--  <h2 class="groupheader">Implementeres ikke</h2>
  <div class="group">
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Terminer - L&oslash;nnsinnberetning</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Overf&oslash;re betaling fra firma</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Innbetaling til firma</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Offentlig Alt Inn</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit">Ut l&oslash;nnsmodul</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Purring p&aring; utest&aring;ende</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">N&oslash;kkeltall</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Restaurant tilleggsskjema</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Varetelling</a>
  <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Likviditet</a>
  </div>
-->
</div>
<div id="lodo_content">
