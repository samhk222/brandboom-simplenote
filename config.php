<?PHP
include("vendor/autoload.php");
include("src/DB.php");
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$sql = [
'createtable' => "CREATE TABLE IF NOT EXISTS `notes` (
    `note_id` INT NOT NULL AUTO_INCREMENT,
    `color` VARCHAR(7) NULL,
    `note` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`note_id`));"
];

$lorem = [
"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin elementum at mi et semper. Quisque pellentesque lectus massa, eu vulputate augue fringilla nec. Proin dictum leo congue nulla ornare, vitae rutrum ipsum suscipit. Nunc in nibh eleifend, imperdiet dui et, luctus lacus. Cras et leo nec tellus porta dictum. Aenean condimentum libero vitae metus vestibulum, nec aliquam diam pulvinar. Ut ex urna, dignissim eu eros non, sagittis sagittis nulla. Maecenas auctor sollicitudin quam vitae commodo. Suspendisse interdum, urna non pulvinar luctus, lacus ex placerat mauris, non eleifend dolor arcu non eros. Cras in eleifend ipsum.",
"In quis orci hendrerit, cursus sapien eget, congue turpis. Curabitur vestibulum, ex non scelerisque interdum, tortor enim congue justo, id mollis felis nunc at felis. Nulla ultricies fermentum finibus. Fusce suscipit elementum augue, in cursus ipsum rutrum sed. Morbi elit elit, auctor vel sapien et, consectetur dapibus quam. Morbi lectus massa, euismo",
"uctor ante eget, consequat varius nibh. Quisque tortor sapien, mollis pellentesque velit ac, blandit tristique neque. Ut elementum nibh ante, ultrices scel",
"a, ac tincidunt sapien molestie. Duis non nisl a elit gravida tincidunt. Proin blandit pulvinar lacus, in tempus enim viverra id. Vestibulum blandit commodo justo in imperdiet. Sed rutrum quis ante ut mattis. Vestibulum sapien ligula, porta ac leo in, dictum lacinia ante. Donec in arcu eros. Aliquam augue libero, ullamcorper ut urna quis, rutrum pellentesque ipsum. Donec justo urna, volutpat eu augue eget, sodales tristique quam. ",
" convallis. Nunc quis efficitur nisl. Sed pulvinar magna in felis pretium, at feugiat mi maximus. Aliquam pharetra neque dui, et consequat dolor pellentesque ut. Pellentesque sagittis condimentum viverra. Nunc congue metus et dui sollicitudin cursus. Vestibulum et tempor odio, a blandit risus. Vestibulum nisl nibh, egestas id hendrerit ac, luctus eu quam. Vivamus pretium, augue eu ullamcorper feugiat, sapien tellus finibus e",
"purus. Sed ut urna leo. Quisque tincidunt mollis nisl id efficitur. Phasellus maximus eleifend vulputate. Praesent sagittis at nisl rhoncus scelerisque. Morbi tincidunt, est nec feugiat dapibus, augue arcu iaculis libero, quis fermentum tortor velit eget lorem. Proin tincidunt lacus a nunc dictu",
"m. Morbi lectus massa, euismod auctor ante eget, consequat varius nibh. Quisque tortor sapien, mollis pellentesque velit ac, blandit tristique neque. Ut elementum nibh ante, ultrices scelerisque felis luctus vitae. Donec volutpat, tortor id feugiat ultrices, nisi leo egestas odio, et lacini",
" justo in imperdiet. Sed rutrum quis ante ut mattis. Vestibulum sapien ligula, porta ac leo in, dictum lacinia ante. Donec in arcu eros. Aliquam augue libero, ullamcorper ut urna quis, rutrum pellentesque ipsum. Donec justo urna, volutpat eu augue eget, sodales tristique quam. Praese",
"s fermentum finibus. Fusce suscipit elementum augue, in cursus ipsum rutrum sed. Morbi elit elit, auctor vel sapien et, consectetur dapibus quam. Morbi lectus massa, euismod auctor ante eget, consequat varius nibh. Quisque tortor sapien, mollis pellentesque velit ac, blandit ",
"Sed ut urna leo. Quisque tincidunt mollis nisl id efficitur. Phasellus maximus eleifend vulputate. Praesent sagittis at nisl rhoncus scelerisque. Morbi tincidunt, est nec",
"Curabitur vestibulum, ex non scelerisque interdum, tortor enim congue justo, id mollis felis nunc at felis. Nulla ultricies fermentum finibus. Fusce suscipit elementum augue, in cursus ipsum rutrum sed. Morbi elit elit, auctor vel sapien et, consectetur dapibus quam. Morbi lectus massa, eu",
"Nunc elementum lorem ut mauris pharetra, ac tincidunt sapien molestie. Duis non nisl a elit gravida tincidunt. Proin blandit pulvinar lacus, in tempus enim viverra id. Vestibulum blandit commodo justo in imperdiet. Sed rutrum quis ante ut mattis. Vestibulum sapien ligula, porta ac leo in, dictum lacinia ante. Donec in arcu eros. Aliquam augue libero, ullamcorper ut urna quis, rutrum pellentesque ipsum. Donec justo urna, volutpat eu augue eget,"
];

$colors = [
    '#CBFF0D',
    '#7AE6FF',
    '#E8950C'
];

foreach ($sql as $key => $value) {
    DB::query($value);
}

$sql_insert = "insert into notes (color, note) values (:color, :note)";
$stmt = DB::prepare($sql_insert);

foreach ($lorem as $key => $value) {
    $stmt->bindValue(':color', $colors[rand(0, count($colors)-1)], PDO::PARAM_INT);
    $stmt->bindValue(':note', $value, PDO::PARAM_INT);
    $stmt->execute();    
}

die("All data was saved. You can close this tab now");