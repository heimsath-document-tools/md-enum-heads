<?php
/**
 *  Enumerate Makrdown headlines in PHP 
 *  Version 1.0
 * 
 * 	Small commandline script to scientifically enumerate headlines in Markdown. 
 *  By Norbert Heimsath (norbert@heimsath.org) 
 * 
 *  For help info just call it:  
 * 		php md-enum-heads.php -h
 * 
 *  Released under GPLv3. For more details see license file 
 */

// Display help
if (count($argv) == 1 || multiple_in_array( array('--help', '-help', '-h', '-?') , $argv)) {
	DisplayHelp($argv);
	die;
} 

// Remove numbering?
$bCleanupMode=false;
if (multiple_in_array(array('--clean', '-c'), $argv))
	$bCleanupMode=true;
    

// Get filenames
$sInFile  = "";
$sOutFile = "";
foreach ($argv as $a) {

	if ($a == $argv[0]) 			continue;      // script filename
	if (preg_match ("/^-/", $a)) 	continue;      // option
	
	if      (empty($sInFile)) 	    $sInFile  = $a;
	else if (empty($sOutFile))       $sOutFile = $a;
	else                            break;         // all done  
}

if (empty($sInFile))  echo "\nNo input file given type:\n    php {$argv[0]} -h\nfor help.\n";

if (empty($sOutFile)) { 
	$sOutFile = $sInFile;
	echo "\nNo output file given, overwriting input file.\n";
}


echo "\n".$sInFile .'  '. $sOutFile."\n" ;

// filehandle 
$hInFile = fopen($sInFile, 'r');

// To cololect outstring
$sOutText="";

// For tracking the level and count of Headlines
$iCountLevel= 0;
$iLevelOld=0;
$iLevel=0;
$aLevel[1]=1;
$aLevel[2]=1;
$aLevel[3]=1;
$aLevel[4]=1;
$aLevel[5]=1;
$aLevel[6]=1;
$aLevel[7]=1;
$aLevel[8]=1;
$aLevel[9]=1;

// Read File line by line 
// And handle headlines
while (!feof($hInFile )) {
	$iCountLevel++;

	$sLine = fgets($hInFile) ;

	// Headline
	if (preg_match("/#+/", $sLine, $Match)) {
		
		// Remove old numbers
		$sLine = preg_replace ("/([#]+)([ 0-9.]+)/", "$1 ", $sLine);
		
		// Init number text 
		$sNumberText="";
		
		// Old and new level
		$iLevelOld=$iLevel;
		$iLevel=strlen($Match[0]);
		echo ("OldLvl $iLevelOld NewLvl $iLevel\n\n");
			
		// More than one level jump?
		// This schould only be ok, when going up 
		// if ($iLevel - $iLevelOld > 1) die("invalid downleveling of headline level: $iCountLevel \nEg. H2 to H6 but next after H2 schould be a H3.   \nin Line $sLine"); 
		
		// When OldLevel > Recent Level do a Reset
		if ($iLevelOld > $iLevel) {
			// Reset for all exept recent Level
			for ($i=$iLevelOld; $i > $iLevel; $i--){
			echo "Rücksetz $i ";
			$aLevel[$iLevelOld] =1;
			}
			$aLevel[$iLevel]++;
		}
		if ($iLevelOld == $iLevel) {$aLevel[$iLevel]++;}
		if ($iLevelOld < $iLevel)  {}	
		
		// Put together the numbertext
		for ($i=1; $i <= $iLevel; $i++){
			//echo "loop$i $iLevel";
			$sNumberText= $sNumberText.$aLevel[$i].".";
		}

		// Add space after number
		$sNumberText.=" ";
		
		// Cleanup mode ?
		if ($bCleanupMode) $sNumberText="";

		$sLine =  preg_replace ("/([#]+)([ ]+)/", "$1 $sNumberText", $sLine);
		
		print_r($aLevel);
		
	} // Ende Preg Match

	echo $sLine;
	
	// Zeilen zusammensetzen
	$sOutText .= $sLine;

}// While lines;
 
// Close Inputfile
fclose($hInFile) ;

// Write output
file_put_contents($sOutFile ,$sOutText);


/**
 * Little function to search if one of multiple needles is present in an array.
 * 
 * Very nice to check if commandline parameters are set in an options list 
 * Return TRUE if any value in neddles exists in haystack or FALSE.
 */
function multiple_in_array($aNeedles, $aHaystack) {
    foreach ($aNeedles as $n) {
		if (in_array($n, $aHaystack))
			return true;
    }
    return false;
}

/**
 *  Display help screen
*/
function DisplayHelp ($argv){
?>

    Markdown scientific numbering for headings.
    ------------------------------------------------
    This is a command line PHP script for adding scientific numbering 
    to the headlines of markdown files.

        Usage:
        php <?php echo $argv[0]; ?> <options> <infile> <outfile> 

        <options> there are multiple options available. 
                  Place em anywhere in the line.
        <infile>  is the file you want to process.
        <outfile> is the file where to store the result.

    Options:
    --help, -help, -h, or -?  ==> Get this help.
    --clean, -c               ==> Remova all Numbering.

<?php
}

