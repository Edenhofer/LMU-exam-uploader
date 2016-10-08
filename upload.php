<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Klausuren Upload</title>
<SCRIPT type="text/javascript"></SCRIPT>
<style></style>
</head>

<body>
	<div align="center">
		<!-- TODO: set cookie which saves the subject -->
		<?php
			// Global values for configuration
			$FILE_DESTINATION_DIR = $_SERVER['DOCUMENT_ROOT'] . "/paul/upl/";
			$MYNAME = "exam_upload";
			$UPLOAD_PASSWORD = "pennyisafreeloader";
			$START_YEAR = 1970;
			$END_YEAR = date("Y");
			$subject_list = array(
				"Physik",
				"Informatik",
				"Nebenfächer",
				"Statistik",
				"Sonstiges");
			$subject_type_list = array(
				"Bachelor",
				"Hauptdiplom",
				"Lehramt",
				"Master",
				"Staatsexamen-schriftlich",
				"Staatsexamen",
				"Vordiplom",
				"Zwischenprüfung",
				"Nebenfach"
			);

			// Enable debugging mode if "debug" is contained in the QUERY_STRING
			$debug = strcasecmp($_SERVER["QUERY_STRING"], "debug");

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				// A post request was sent (a file is being uploaded)

				// Check password and exit if it was wrong
				$passwd = $_POST["{$MYNAME}_main_password"];
				if (strcasecmp($passwd, $UPLOAD_PASSWORD) != 0) {
					echo "<p>Komm schon... das Passwort ist doch quasi öffentlich, das solltest du wirklich kennen.</p>";
					exit;
				}

				// Check the input for manipulation
				// TODO
				$semester = $_POST["{$MYNAME}_main_semester"];
				$lectur = $_POST["{$MYNAME}_main_lectur"];
				$lecturer = $_POST["{$MYNAME}_main_lecturer"];
				$subject = $_POST["{$MYNAME}_main_subject"];
				$subject_type = $_POST["{$MYNAME}_main_subject_type"];
				$exam_type = $_POST["{$MYNAME}_main_exam_type"];

				// Create appropiate directories
				if (!file_exists("$FILE_DESTINATION_DIR/$subject/$subject_type/$lectur/"))
					mkdir("$FILE_DESTINATION_DIR/$subject/$subject_type/$lectur/", 0777, true);

				// Define where the file belongs
				$file_destination = "$FILE_DESTINATION_DIR/$subject/$subject_type/$lectur/" . substr($semester, -2) . "-{$exam_type} - {$lecturer}.pdf";

				// Move the file in place in case it was not already uploaded
				if (file_exists("$file_destination")) {
					echo "<p>Diese Datei wurde bereits hoch geladen.</p>\n<br>";
				} else {
					if (move_uploaded_file($_FILES["{$MYNAME}_main_file"]['tmp_name'], $file_destination)) {
							echo "Danke fürs Hochladen!<br>\n";
					} else {
							echo "Possible file upload attempt!<br>\n";
					}
				}

				// Show debug output if requested
				if ($debug == 0) {
					echo "<p><pre>\n"
						. var_dump($_FILES) . "\n"
						. "name = " . $_FILES["{$MYNAME}_main_file"]['name'] . "\n"
						. "tmp_name = " . $_FILES["{$MYNAME}_main_file"]['tmp_name'] . "\n"
						. "destination = " . $file_destination . "\n"
						. "semster = " . $semester . "\n"
						. "subject = " . $subject  . "\n"
						. "subject_type = " . $subject_type . "\n"
						. "lecturer = " . $lecturer . "\n"
						. "exam_type = " . $exam_type . "\n"
						. "</pre></p>\n<br>\n";
				}
			} else {
				// A normal, non-post request was sent hence display a form to upload a file

				// Print document upload form
				echo "<form enctype=\"multipart/form-data\" name={$MYNAME}_main_upload  method=POST action="
					. htmlspecialchars($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'] . ">"
					. "<p>\n\tKlausuren, Lösungen, etc. (ausschließlich im pdf Format): <input type=file name={$MYNAME}_main_file />\n</p>";

				// Print years and semesters as select element
				echo "<p>\n\tJahrgang: <select name={$MYNAME}_main_semester>\n\t\t<option value=\"na\">Nicht Bekannt</option>\n";
				for ($year=$END_YEAR; $year>=$START_YEAR; $year--) {
					echo "\t\t<option value=\"$year\">WS " . substr($year, -2) . "/" . substr(($year+1), -2) . "</option>\n";
					echo "\t\t<option value=\"$year\">SS " . substr($year, -2) . "</option>\n";
				}
				echo "</select></p>\n";

				// Print subjects as select element
				echo "<p>\n\tStudiengang: <select name={$MYNAME}_main_subject>\n";
				foreach ($subject_list as &$subject)
					echo "\t\t<option value=\"$subject\">$subject</option>\n";
				unset($subject);
				echo "</select></p>\n";

				// Print subjects type as select element
				echo "<p>\nStudiengang Typ: <select name={$MYNAME}_main_subject_type>\n";
				foreach ($subject_type_list as &$subject_type)
					echo "\t\t<option value=\"$subject_type\">$subject_type</option>\n";
				unset($subject_type);
				echo "</select></p>\n";

				// Print name of the lectur, lecturer, exam type and passowrd input fields
				echo "<p>\n\tVorlesung (Bitte Abkürzungen verwenden, e.g. E1, M1, T0): "
					. "<input type=text name={$MYNAME}_main_lectur maxlength=10 autocomplete=\"off\"></p>\n"
					. "<p>\n\tNachname des Dozents (keine Ehrentitel o.ä.): "
					. "<input type=text name={$MYNAME}_main_lecturer maxlength=20 autocomplete=\"off\"></p>\n"
					. "<p>\n\tKlausurtyp (Lösungsskizzen, Miniklausur, Hauptklausur 1/2 etc.): "
					. "<input type=text name={$MYNAME}_main_exam_type maxlength=50 placeholder=\"Hauptklausur 1 + Lösungsskizze\"></p>\n"
					. "<p>\n\tPasswort: <input type=password name={$MYNAME}_main_password>\n</p>\n"
					. "<p>\n\t<button name=submit valu=submit type=submit>Hochladen</button>\n</p>\n"
					. "</form>";
			}
		?>
	</div>
</body>
</html>
