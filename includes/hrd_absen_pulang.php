<? 
$PID = "hrd_absen_pulang";
$SC = $_SERVER["SCRIPT_NAME"];

unset($_SESSION["IBU"]["id"]);
unset($_SESSION["IBU"]["nama"]);
		
require_once("lib/dbconn.php");
require_once("lib/form.php");
require_once("lib/class.PgTable.php");
require_once("lib/functions.php");
     
if (strlen($_GET["registered"]) == 0) $_GET["registered"] = "Y";
   title(" <img src='icon/informasi-2.gif' align='absmiddle' > ABSENSI PEGAWAI PULANG");
   echo "<table width='100%' cellspacing=0 cellpadding=2><td CLASS='PAGE_TITLE'></td>\n";
   //echo "<td width=1 align=right><a href=\"index2.php?p=hrd_edit_jadwal\"><img border=0 src=\"icon/log_message.png\" title=\"edit\" ></a></td>\n";
   echo "&nbsp";
   echo "<td width=1 align=right><a href=\"index2.php?p=hrd_absen\"><img border=0 src=\"icon/edit_small.png\" title=\"masuk\" ></a></td>\n";
   echo "</table>\n";
//echo "<br>";

$f = new Form($SC, "GET", "NAME=Form2");
$f->hidden("p",$PID);
$f->hidden("q","search");
if ($_GET["registered"] == "Y" && $_GET["q"] != "reg") {
$f->text("search","Nama / NIP",40,40,$_GET["search"]);
$f->submit("Tampilkan");
}
$f->execute();

$psn = "<font color='red'>{$_GET['psn']}</font>";
$psn2 = "<font color='red'>{$_GET['psn2']}</font>";

if ($_GET["q"] == "reg") {
    //$r = pg_query($con, "select * from hrd_absen where id_pegawai = '".$_GET["id"]."' and tanggal = '".date("Y-m-d", time())."' and shift = '".$_GET["shift"]."'");
      //  $n = pg_num_rows($r);
        //if($n > 0) $d = pg_fetch_object($r);
        //pg_free_result($r);
	//$cek = getfromtable("select ");
        //$SQL = "update hrd_absen set status = '001' where id_pegawai = '".$_GET["id"]."' and shift = '".$_GET["s"]."' and tanggal = '".date("Y-m-d", time())."' ";
        $tglhariini = date("d-m-Y", time());
        $tD = date("d", time());
	$tM = date("m", time());
	$tY = date("Y", time());
        $tglbesok = date("d-m-Y", (mktime(0,0,0,$tM,$tD-1,$tY)));
        $jam 	= getFromTable("select to_char(CURRENT_TIMESTAMP,'HH24:MI:SS') as jam");
        if ($_GET["pulang"] < $_GET["masuk"]  ){
            if (($jam < $_GET["pulang"]and $_GET["hari"] == $tglbesok) or ($jam > $_GET["masuk"] and $_GET["hari"] == $tglhariini )){
            echo "<script>alert(\"Anda belum bisa menginputkan absen pulang karena belum waktunya.\");</script>";
            exit;
            }elseif($jam < $_GET["masuk"] and $_GET["hari"] == $tglhariini){
            echo "<script>alert(\"Anda belum bisa menginputkan absen pulang karena belum waktunya.\");</script>";
            exit;
            }else{
            
            }
        }elseif ($jam > $_GET["pulang"]  ){
            
        }elseif ($jam > $_GET["masuk"] and $_GET["hari"] == $tglhariini ){
            echo "<script>alert(\"Anda belum bisa menginputkan absen pulang karena belum waktunya.\");</script>";
            exit;
        }elseif ($jam < $_GET["masuk"] and $_GET["hari"] == $tglhariini  ){
            echo "<script>alert(\"Anda belum bisa menginputkan absen pulang karena belum waktunya.\");</script>";
            exit;
        }else{

        }
        
        $SQL = "update hrd_absen set waktu_pulang = '$jam' where id = '".$_GET["id"]."' ";
        //$SQL1 = "update hrd_absen set waktu_absen = '$jam' where id = '".$_GET["id"]."' ";
        pg_query($con, $SQL);
        //pg_query($con, $SQL1);
        //header("Location: ../index2.php?p=$PID");
        echo "<script>alert(\"Anda sudah terhitung pulang.\");</script>";
        exit;
        /*echo "<script language=javascript>\n";
        echo "<!--\n";
        echo "window.location=\"index2.php?p=$PID&registered=Y&q=search\";\n";
        echo "-->\n";
        echo "</script>\n";*/
        
} else {
	if ($_GET["registered"] == "Y" && $_GET["q"] == "search" && strlen($_GET["search"]) > 0) {
    	$t = new PgTable($con, "100%");
        $tglhariini = date("Y-m-d", time());
        $tD = date("d", time());
	$tM = date("m", time());
	$tY = date("Y", time());
        $tglbesok = date("Y-m-d", (mktime(0,0,0,$tM,$tD-1,$tY)));
        $t->SQL =
            "select a.nip,a.nama, e.shift, ".
                "case when b.tempat_bangsal = '' and b.tempat_poli != '' then c.tdesc ".
		"     when b.tempat_bangsal != '' and b.tempat_poli = '' then d.bangsal ".
                "     when b.tempat = 'I' then 'IGD' ".
                "     when b.tempat = 'K' then 'Kantor' ".
                "else 'Non-Medis' end , to_char(b.tanggal,'DD-MM-YYYY') as tgl ,  ".
                "e.jm_mulai,e.jm_selesai,  ".
                "b.id as href ".
	        "from rs00017 a , hrd_absen b ".
	        "left outer join hrd_shift e ON b.shift = e.code  ".
	        "left outer join rs00012 d ON b.tempat_bangsal = d.hierarchy ".
	        "left outer join rs00001 c ON b.tempat_poli = c.tc and c.tt='LYN' ".
                "where (upper(a.nama) LIKE '%".strtoupper($_GET["search"])."%') and (b.status = '001' or b.status = '002') ".
                "and (b.tanggal = '$tglhariini'or b.tanggal = '$tglbesok') and a.id = b.id_pegawai and b.waktu_pulang is null  ";
                	
		if (!isset($_GET[sort])) {
        	$_GET[sort] = "a.nip";
           	$_GET[order] = "asc";
		}
        $t->ColHeader = array("NRP/NIP","NAMA", "SHIFT","TEMPAT","TANGGAL","MULAI","SELESAI", "ABSEN");
        $t->ShowRowNumber = true;
        $t->ColAlign[0] = "CENTER";
        $t->ColAlign[2] = "CENTER";
        $t->ColAlign[5] = "CENTER";
        $t->ColAlign[7] = "CENTER";
        $t->RowsPerPage = 10;
	$t->ColFormatHtml[7] = "<nobr>
	<A CLASS=TBL_HREF "."HREF='$SC?p=$PID&q=reg&hari=<#4#>&masuk=<#5#>&pulang=<#6#>&id=<#7#>'>".icon("ok","Pulang")."</A>
        </nobr>";
        
        $t->execute();
    }
}

?>
