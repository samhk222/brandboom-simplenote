<?PHP
include("vendor/autoload.php");
include("src/DB.php");
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ret['success']=true;        
    if ($_GET['action']=='SubmitNote') {
        if ($_POST['hnd_note_id'] == '0') {
            $ret['parm']='adding';
            $sql_insert = "insert into notes (color, note) values (:color, :note)";
            $stmt = DB::prepare($sql_insert);
            $stmt->bindValue(':color', $_POST['color'], PDO::PARAM_STR);
            $stmt->bindValue(':note', $_POST['note'], PDO::PARAM_INT);            
        } else {
            $ret['parm']='editing';
            $sql_insert = "update notes set color = :color, note = :note where note_id = :note_id";
            $stmt = DB::prepare($sql_insert);
            $stmt->bindValue(':color', $_POST['color'], PDO::PARAM_STR);
            $stmt->bindValue(':note', $_POST['note'], PDO::PARAM_INT);            
            $stmt->bindValue(':note_id', $_POST['hnd_note_id'], PDO::PARAM_INT);            
        }
        $stmt->execute();            
        echo json_encode($ret);
        exit();        
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $ret['success']=true;
    if ($_GET['action'] == 'delete') {
        $sql = "delete from notes where note_id = :note_id";
        $stmt = DB::prepare($sql);
        $stmt->bindValue(':note_id', $_GET['note_id'], PDO::PARAM_INT);
        $stmt->execute();    
        echo json_encode($ret);
        exit();
    }
    if ($_GET['action'] == 'Edit') {
        $sql = "select * from notes where note_id = :note_id order by updated_at DESC";
        $stmt = DB::prepare($sql);
        $stmt->bindValue(':note_id', $_GET['note_id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        echo json_encode($row);
        exit();
    }
    if ($_GET['action'] == 'getNotes') {
        $sql = "select * from notes order by updated_at DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        if ($rows){
            foreach ($rows as $key => $row) {
                $ret['html'] .=<<<EOL
                <div class="card cardMargin" style="width: 15rem; background-color: {$row['color']};" id="noteid_{$row['note_id']}">
                  <div class="card-body">
                    <p class="card-text">{$row['note']}</p>
                    <a href="#" class="card-link text-center">
                        <a class="btn btn-primary btn-sm" href="#" onclick="javascript:Edit({$row['note_id']})"> <i class="fa fa-pencil"></i> Edit</a>
                        <a class="btn btn-danger btn-sm" href="#" onclick="javascript:Delete({$row['note_id']})"> <i class="fa fa-trash-o fa-lg"></i> Delete</a></a>
                  </div>
                </div>
EOL;
            }
        } else {
          
        }

        echo json_encode($ret);
        exit();
    }
}

$colors = [
    'green' => ['rgb'=>'#CBFF0D','textColor'=>'black'],
    'blue' => ['rgb'=>'#7AE6FF','textColor'=>'black'],
    'orange' => ['rgb'=>'#E8950C','textColor'=>'white'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' />

    <style type="text/css">
        .actionBar {
            background-color: white;
            padding: 5px;
        }
        .cardMargin {
            margin-left: 7px;
            margin-bottom: 7px;
        }
    </style>

    <title>Simplenotes!</title>
  </head>
  <body>
    
    <div class='container-fluid'>
        

        <div class="sticky-top actionBar">

            <form id="frm1" onsubmit="return false;">
                <input type='hidden' name='hnd_note_id' id='hnd_note_id' value='0' />
              <div class="row">
                <div class="col col-md-8">
                  <input type="text" id="note" name="note" class="form-control" placeholder="Please enter your note">
                </div>
                <div class="col col-md-2">
                    <select name="color" id="color" class="form-control" >
                        <?PHP
                        foreach ($colors as $color => $value) {
                            echo "<option value='{$value['rgb']}' style='background-color: {$value['rgb']}; color:{$value['textColor']}'>{$color}</option>";
                        }
                        ?>
                   </select>
                </div>
                <div class="col col-md-2">
                 <input type='submit' name='btn_Action' id='btn_Action' class='btn btn-primary' value='Add' />
                </div>

              </div>
            </form>


        </div>


        <div class="row" id="notes">
        </div>

    </div>
    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        setState('add');
        getNotes();
    });

    $('#btn_Action').on('click', function() {
        $.ajax({
            type: "POST",
            url: "?action=SubmitNote",
            data: $("#frm1").serialize(),
            dataType: 'json',
            success: function(ret) {
                if (ret.success){
                    //alert( "Data Saved: " + ret.parm );
                    setState("add");
                    getNotes();
                }
            },
            beforeSend: function() {

            }
        });
    });

    function setState(state) {
        if (state == 'add') {
            $('#btn_Action').val('Add');
            $('#hnd_note_id').val('0');
            $('#note').val('').focus();
        } else if (state == 'edit') {
            $('#btn_Action').val('Save');
        }
    }

    function getNotes() {
        $.getJSON('?action=getNotes', {}, function(ret) {
            $('#notes').html(ret.html);
        })
    }

    function Edit(note_id) {
        setState('edit');
        scroll(0,0);
        $.getJSON('?action=Edit', {note_id:note_id}, function(ret) {
            setState('edit');
            $('#hnd_note_id').val(note_id);
            $('#color').val(ret.color);
            $('#note').val(ret.note).select();
        });        
    }
    function Delete(note_id) {
        if (confirm("Are you sure ?")) {
            $.getJSON('?action=delete', {note_id:note_id}, function(ret) {
                $('#noteid_' + note_id).remove();
                setState('add');
            });
        }
    }
    </script>
  </body>
</html>