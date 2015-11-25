<?php

    $__ROOT__ = dirname(__FILE__)."/..";

    require_once $__ROOT__."/lib/lib.php";

    if(!isset($_SESSION))
    {
        session_start();
    }

    if(isset($_GET["logout"]))
    {
        $_SESSION["log"] = 0;
        header("Location: ..");
        die();
    }
    else
    {
        $db = getConnexion();

        if($db)
        {
            if(isset($_POST['pass']) and isset($_POST['user']))
            {
                $pw = $_POST['pass'];
                $user = $_POST['user'];

                $hash = getHash($pw, $user);

                $sql = "SELECT api_key FROM users where hash = :hash and user = :user";

                $stmt = $db->prepare($sql);

                if($stmt->execute(array("hash"=>$hash, "user" => $user)))
                {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if($row)
                    {
                        $_SESSION['log'] = 1;
                        $_SESSION["api"] = $row["api_key"];;
			header("Location: ../");
                        die();
                    }
                    else
                    {
                       include($__ROOT__."/includes/error-login.html");
                    }
                }
            }
            else
            {
                echo("Error");
            }
        }
        else
        {
            echo("Error connecting database");
        }
    }
?>
