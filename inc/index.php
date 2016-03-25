<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="Content-type" charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Refresh" CONTENT="20; url=index.php">  <!-- La page web se rafraichie toutes les 60 sec -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dashboard GLPI">
    <meta name="author" content="Miguel Francisco">
    <link rel="icon" href="../img/favicon.ico">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/min.css">
	
	
    <title>Affichage GLPI</title>

      <script type="text/javascript" src="../js/script.js"></script>
                 
  </head>
     
  <body>
      
                 <!---------------------------------------------------->
                 <!-- Navigateur Affichage GLPI (Bande noir du haut) -->
                 <!---------------------------------------------------->
    
    <nav class="navbat navbar-inverse navbar-fixed-top" style="height: 40px;">
      <div class="container-fluid">
        <div class="navbar-header">
            <img class="logoglpi" src="../img/icon.png">
          <a class="navbar-brand"> Dashboard </a>
        </div>
      </div>
    </nav>
      
             <!-------------------------------------------------------------->
             <!-- Dashboard, diagrames camembert des priorités des tickets -->  
             <!-------------------------------------------------------------->
  
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Stats tickets, Nagios</h1>
          <div class="row placeholders">
              
              
             <!---------------------------------------------------------------->
             <!---------------- Diagramme Priorités Tickets ------------------->
             <!---------------------------------------------------------------->
              
              <div class="col-xs-6 col-sm-3 placeholder">
                
            <canvas id="id_canvas" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail"></canvas>
                
                <h4>Priorités</h4>
                <span class="text-muted">Tickets</span>
              </div>    
              
             <!---------------------------------------------------------------->
             <!---------------- Affichage interface Nagios3 ------------------->
             <!---------------------------------------------------------------->
              
             <div class= "table-nagios3"> 
                 
                <iframe id="frame-nagios" name="nagiosframe" frameborder="0" style="width:100%; height: 300px; marginheight: 0px; marginwidth: -5px;" src="http://172.16.0.9/cgi-bin/nagios3/status.cgi?host=all&servicestatustypes=16"></iframe>
    
            </div>
                 
             <!---------------------------------------------------------------->
             <!----------------- Informations des tickets --------------------->
             <!---------------------------------------------------------------->
                    
            
          <h1 class="sub-header" id="id_ticket">Tickets</h1>
            
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                    
                  <th>ID</th> <!-- id -->
                  <th>Titre</th> <!-- name -->
                  <th>Statut</th> <!-- status -->
                  <th>Date d'ouverture</th> <!-- date -->
				  <th>Dernière modification</th> <!-- date_mod -->
                  <th>Priorité</th> <!-- priority -->
                  <th>Demandeur</th> <!-- firstname et realname type 1 --> 
             <!-- <th>Technicien</th> --> <!-- firstname et realname type 2 --> 
			
                </tr>
              </thead>
            <tbody>
           <tr>
        
                
             <!---------------------------------------------------------------->
             <!---- Connexion Base de données, requête et script diagramme ---->
             <!---------------------------------------------------------------->
                
 <?php
 
// Connexion à la bdd
$bdd = new PDO ('mysql:dbname=glpi;host=172.16.0.5', "kibana", "psg");
                  
// Création de la requête SQL
$sql="Select glpi_tickets.id, glpi_tickets.name, glpi_tickets.status, 
             glpi_tickets.date, glpi_tickets.date_mod,glpi_tickets.priority, 
             glpi_tickets.solve_delay_stat, glpi_users.firstname, glpi_users.realname
      FROM glpi_tickets, glpi_users, glpi_tickets_users 
      WHERE glpi_users.id = glpi_tickets_users.users_id 
      AND glpi_tickets.id = glpi_tickets_users.tickets_id
      AND glpi_tickets_users.type='1'
      AND glpi_tickets.status < '6' ORDER BY glpi_tickets.date DESC;";
                  
// Envoi de la requête
$req=$bdd->query($sql) or die('Erreur SQL1 !<br>'.$sql.'<br>'.mysql_error());

// Attribue un nom à la valeur de status                
$statut_tickets=array("Rien","Nouveau","En cours - Attribué","En cours - Planifié","En attente","Résolu","Clos");        

// Attribue un nom à la valeur de priority
$prio_tickets=array("Sans importance","Très basse","Basse","Moyenne","Urgent","Très urgent");
               
// Attribution des couleur priorité                
$couleur_prio=array("#028eaf", "#5db56a", "#1e893f", "#ffbc00", "#e54b25", "#c01617");

$nbre_priorites=array(0,0,0,0,0,0);
$nbre_priorites_total=0; 

               
$nbre_statut=array(0,0,0,0,0,0,0); 

// Boucle qui permet d'afficher chaque enregistrement
while ($row = $req->fetch()) {
      
     $nbre_priorites[$row['priority']]++;
     $nbre_priorites_total++;
    
     $nbre_statut[$row['status']]++; // Calcul total des tickets
         
      echo '<tr>
      <td>'.$row['id'].'</td>
      <td class="tdtitre">'.utf8_encode($row['name']).'</td>
      <td>'.$statut_tickets[$row['status']].'</td>
      <td>'.$row['date'].'</td>
      <td>'.$row['date_mod'].'</td>
      <td class="tdpriority"><font color='.$couleur_prio[$row['priority']].'>'.$prio_tickets[$row['priority']].'</font></td>
      <td>'.utf8_encode($row['firstname']).' '.utf8_encode($row['realname']).'</td></tr>';
    
       // Calcule de la sum
       $ticketstotal = array_sum($nbre_statut);
           
} 
               
// echo '<h1> ['.$ticketstotal.'] </h1>';  
               
// Fermeture de la connexion               
$pdo = null;
    
// Magouille de Vincent, Diagrame Prio               
$ma_datalist="datalist=new Array(";                
$debut=true;
foreach ($nbre_priorites as $value) {
    if ($debut == false) {$ma_datalist.=",";}
    $ma_datalist.=$value;
    $debut=false;
}
    $ma_datalist.=")";
                
// Affichage diagramme tickets               
echo "<script>$ma_datalist;mon_cercle(datalist);</script>";

// Affichage du nombre total de tickets
echo "<script>total_tickets($ticketstotal);</script>";

?>
      
                 </tr>
               </tbody>
             </table>
           </div>
         </div>
       </div>
      </div>
  </body>
</html>