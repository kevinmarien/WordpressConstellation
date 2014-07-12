<?php
/*
Plugin Name: Constellation
Description: Liste des constellations
Version: 0.1
License: GPL
Author: Kevin Marien, Jonathan Esedji
*/
if ( !class_exists("Constellation") )
{
    /**** Objet pour gérer la constellation ****/
    class Constellation
    {
        /**** Créer la table si elle n'existe pas en base ****/

        public function install() {
            global $wpdb;
            $query = "CREATE TABLE ".$wpdb->prefix."collection (id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, nom VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, couleur VARCHAR(7) NOT NULL, photo VARCHAR(255) NOT NULL, type VARCHAR(150) NOT NULL, date DATE NOT NULL, visible BOOL NOT NULL) CHARACTER SET utf8 COLLATE utf8_general_ci";
            $wpdb->query($query);
            add_filter( 'cron_schedules', 'cron_minutes' );
            do_cron();
        }

        /*****************************************************/

        /**** Supprime la table ****/
        
        public function uninstall() {
            global $wpdb;
            $query = "DROP TABLE ".$wpdb->prefix."collection";
            $wpdb->query($query);
        }

        /***************************/
        

        /**** Insère en base les différents champs ****/
            
        public function createConstellation() {
            global $wpdb;
            $query = "INSERT INTO ".$wpdb->prefix."collection (nom, description, couleur, photo, type, date, visible)
                    VALUES ('".$_POST['nom']."',  '".$_POST['description']."',  '".$_POST['couleur']."',  '".$_POST['photo']."',  '".$_POST['type']."',  '".$_POST['date']."',  ".$_POST['visible'].");";
            if($wpdb->query($query)){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
            else{
                echo "Erreur";
            }
        }
        

        /**********************************************/

        /**** Modifier les champs ****/
            
        public function updateConstellation() {
            global $wpdb;
            $query = "UPDATE ".$wpdb->prefix."collection SET nom = '".$_POST['nom']."', description = '".$_POST['description']."', couleur = '".$_POST['couleur']."', photo = '".$_POST['photo']."', type = '".$_POST['type']."', date = '".$_POST['date']."', visible = ".$_POST['visible']." WHERE id = ".$_POST['idConstellation'];
            if($wpdb->query($query)){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
            else{
                echo "Erreur";
            }
        }


        /***********************************************/

        /**** Suprimer une entrée dans la table ****/
        
        public function deleteConstellation($arg1) {
            global $wpdb;
            $query = "DELETE FROM ".$wpdb->prefix."collection WHERE id = '".$arg1."'";
            if($wpdb->query($query)){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
            else{
                echo "Erreur";
            }
        }

        /********************************************/
    }
 
    function cron_minutes( $schedules ) {
        // Adds once weekly to the existing schedules.
        $schedules['minutes'] = array(
            'interval' => 1,
            'display' => __( 'Toutes les minutes' )
        );
        return $schedules;
    }

    function do_cron(){
        wp_schedule_event( time(), 'minutes', 'my_task_hook' );
        //wp_cron();
    }

    /********************************************/
    
    /*
        Ajoute au menu 'Liste des constellations'
        add_options_page( 'arg1', 'arg2', 'arg3', arg4, 'arg5' );
        arg1 : Description de l'extension dans la liste des extensions
        arg2 : Nom du lien dans le menu 'Réglages'
        arg3 : Indique qu'il faut les droits d'admin pour accéder à cette page
        arg4 : Nom de la page (ici on récupère le nom de notre fichier 'constellation.php')
        arg5 : Nom de la fonction qui affiche les données
    */

    function Constellation_menu() {
       add_options_page( 'Liste des constellations', 'Listes des constellations', 'manage_options', __FILE__, 'Constellation_admin' );
    }

    /*
        Génère le tableau des constellations
    */
    function get_constellations($atts){
        global $wpdb;

        //Assigne dans un tableau les attributs shortcodes utilisés
        $attributs = shortcode_atts( array('filtre' => 'all', 'total' => 'false'), $atts );

        switch ($attributs['filtre']) {
            case 'noms':
                // Récupération de toutes les constellations
                $query = $wpdb->prepare("SELECT id, nom FROM ".$wpdb->prefix."collection ORDER BY id DESC", 1);
                $constellations = $wpdb->get_results($query);
                $rowcount = $wpdb->num_rows;

                if($rowcount > 0){ 
                    ?>
                    <h2><?php echo $title;?></h2>
                    <table class="wp-list-table widefat media" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="nom" class="manage-column column-title check-column" style="">
                                    <p>Liste des constellations</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="the-list">
                        <?php
                            // Pour tous les résultats on affiche une ligne avec les données d'afficher
                            foreach ($constellations as $constellation) { ?>
                                <tr id="<?php echo $constellation->id; ?>" class="alternate author-self status-inherit" valign="top">
                                    <td scope="row" class="title column-title">
                                        <p><?php echo $constellation->nom; ?></p>
                                    </td>
                                </tr>
                            <?php
                            }
                           ?>
                        </tbody>
                    </table>
                    <?php
                    // Affichage du nombre de constellations si le paramètre du shortcode 'total' vaut 'true'
                    if($attributs['total'] == 'true'){
                        ?><span style="font-size: 0.7em; line-height: 1.3em; float: right;">
                            <?php
                                if($rowcount > 1){
                                    echo $rowcount." constellations.";
                                }
                                else{
                                    echo $rowcount." constellation.";
                                }
                            ?>
                        </span><?php
                    }
                }
                break;
            case 'noms_descriptions_types':
                $query = $wpdb->prepare("SELECT id, nom, description, type FROM ".$wpdb->prefix."collection ORDER BY id DESC", 1);
                $constellations = $wpdb->get_results($query);
                $rowcount = $wpdb->num_rows;

                if($rowcount > 0){ 
                    ?>
                    <h2><?php echo $title;?></h2>
                    <table class="wp-list-table widefat media" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="nom" class="manage-column column-title check-column" style="">
                                   <p>Nom</p>
                                </th>
                                <th scope="col" id="desc" class="manage-column column-title" style="">
                                   <p>Description</p>
                                </th>
                                <th scope="col" id="type" class="manage-column column-title" style="">
                                   <p>Type</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="the-list">
                        <?php
                            foreach ($constellations as $constellation) { ?>
                                <tr id="<?php echo $constellation->id; ?>" class="alternate author-self status-inherit" valign="top">
                                    <td scope="row" class="title column-title">
                                        <p><?php echo $constellation->nom; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php echo $constellation->description; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php echo $constellation->type; ?></p>
                                    </td>
                                </tr>
                            <?php
                            }
                           ?>
                        </tbody>
                    </table>
                    <?php
                    if($attributs['total'] == 'true'){
                        ?><span style="font-size: 0.7em; line-height: 1.3em; float: right;">
                            <?php
                                if($rowcount > 1){
                                    echo $rowcount." constellations.";
                                }
                                else{
                                    echo $rowcount." constellation.";
                                }
                            ?>
                        </span><?php
                    }
                }
                break;
            
            default:
                $query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."collection ORDER BY id DESC", 1);
                $constellations = $wpdb->get_results($query);
                $rowcount = $wpdb->num_rows;

                if($rowcount > 0){ 
                    ?>
                    <h2><?php echo $title;?></h2>
                    <table class="wp-list-table widefat media" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="nom" class="manage-column column-title check-column" style="">
                                   <p>Nom</p>
                                </th>
                                <th scope="col" id="desc" class="manage-column column-title" style="">
                                   <p>Description</p>
                                </th>
                                <th scope="col" id="couleur" class="manage-column column-title" style="">
                                   <p>Couleur</p>
                                </th>
                                <th scope="col" id="photo" class="manage-column column-title" style="">
                                   <p>Photo</p>
                                </th>
                                <th scope="col" id="type" class="manage-column column-title" style="">
                                   <p>Type</p>
                                </th>
                                <th scope="col" id="date" class="manage-column column-title" style="">
                                   <p>Date</p>
                                </th>
                                <th scope="col" id="visible" class="manage-column column-title" style="">
                                   <p>Visible</p>
                                </th>
                                <!-- Si l'internaute est un admin et qu'il est dans le back office, on lui affiche les btn de modification et suprpession de constellation -->
                                <?php if((current_user_can( 'manage_options' )) && (is_admin()) ){ ?>
                                    <th scope="col" id="visible" class="manage-column column-title" style="">
                                        <p>Actions</p>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody id="the-list">
                        <?php
                            foreach ($constellations as $constellation) { ?>
                                <tr id="<?php echo $constellation->id; ?>" class="alternate author-self status-inherit" valign="top">
                                    <td scope="row" class="title column-title">
                                        <p><?php echo $constellation->nom; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php echo $constellation->description; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <?php if($constellation->couleur){ ?>
                                            <p><?php echo $constellation->couleur; ?></p>
                                        <?php } ?>
                                    </td>
                                    <td class="column-icon media-icon">
                                        <?php if($constellation->photo){ ?>
                                            <img width="60" height="60" src="<?php echo $constellation->photo; ?>" class="attachment-80x60" alt="<?php echo $constellation->nom; ?>">
                                        <?php } ?>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php echo $constellation->type; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php echo $constellation->date; ?></p>
                                    </td>
                                    <td class="title column-title">
                                        <p><?php 
                                            if($constellation->visible == "1"){
                                                echo "Oui";
                                            }
                                            else{
                                                echo "Non";
                                            } ?></p>
                                    </td>
                                    <?php if((current_user_can('manage_options')) && (is_admin()) ){ ?>
                                        <td class="title column-title">
                                            <form id="delete" method="post">
                                                <input type="hidden" name="idConstellation" value="<?php echo $constellation->id; ?>">
                                                <input type="submit" name="deleteConstellation" id="deleteConstellation" value="Supprimer">
                                            </form>
                                            <form id="update" method="post">
                                                <input type="hidden" name="id" value="<?php echo $constellation->id; ?>">
                                                <input type="hidden" name="nom" value="<?php echo $constellation->nom; ?>">
                                                <input type="hidden" name="description" value="<?php echo $constellation->description; ?>">
                                                <input type="hidden" name="couleur" value="<?php echo $constellation->couleur; ?>">
                                                <input type="hidden" name="photo" value="<?php echo $constellation->photo; ?>">
                                                <input type="hidden" name="type" value="<?php echo $constellation->type; ?>">
                                                <input type="hidden" name="date" value="<?php echo $constellation->date; ?>">
                                                <input type="hidden" name="visible" value="<?php echo $constellation->visible; ?>">
                                                <input type="submit" name="updateConstellation" id="updateConstellation" value="Modifier">
                                            </form>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php
                            }
                           ?>
                        </tbody>
                    </table>
                    <?php
                    if($attributs['total'] == 'true'){
                        ?><span style="font-size: 0.7em; line-height: 1.3em; float: right;">
                            <?php
                                if($rowcount > 1){
                                    echo $rowcount." constellations.";
                                }
                                else{
                                    echo $rowcount." constellation.";
                                }
                            ?>
                        </span><?php
                    }
                }
                break;
        }
     }
    
    /*
        Contenu afficher dans la page d'administration des constellations (admin)
    */
    function Constellation_admin() {
        global $title;
        // Si l'internaute n'est pas un admin on lui affiche un message d'erreur
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( "Vous n'avez pas les permissions pour accéder à cette page." ) );
        }
        // Si on n'a pas cliqué sur le btn d'ajout d'une constellation ou le btn de suppression d'une constellation, on affiche toutes les constellations existantes + le formulaire d'ajout
        if (!isset($_POST['submitConstellation']) && !isset($_POST['deleteConstellation'])) {
            // Affiche le tableau des constellations avec le paramètre 'all' qui correspond au filtre du shortcode
            get_constellations('all');
            ?>
            <hr>
            <h2>Ajouter une constellation</h2>
            <form method="POST" id="addConstellation">
                <label for="nom">Nom de la constellation :</label> <input name="nom" id="nom" type="text" maxlength="150" required><br>
                <label for="description">Description :</label> <textarea name="description" id="description" maxlength="255"></textarea><br>
                <label for="couleur">Couleur :</label> <input type="color" name="couleur" id="couleur" maxlength="6"><br>
                <label for="photo">Url de la photo :</label> <input name="photo" id="photo" type="text" maxlength="255"><br>
                <label for="type">Type :</label>
                <select name="type">
                    <option value="Perseus">Perseus</option>
                    <option value="La Caille">La Caille</option>
                    <option value="Bayer">Bayer</option>
                    <option value="Zodiac">Zodiac</option>
                    <option value="Hercules">Hercules</option>
                </select><br>
                <label for="date">Date :</label> <input type="date" name="date" id="date" required><br>
                <label for="visible">Est-elle visible depuis la Terre :</label> <input name="visible" value="1" type="radio"> Oui <input name="visible" value="0" type="radio" checked="checked"> Non <br>
                <input type="submit" name="submitConstellation">
            </form>
            <?php
            // Sinon, si on a cliqué sur le btn d'ajout de constellation, on appel l'action 'createConstellation'
        } elseif (isset($_POST['submitConstellation'])) {
            // On appel l'action intitulée 'createConstellation'
            do_action('createConstellation');
            // Sinon, si on a cliqué sur le btn de suppression de constellation, on appel l'action 'deleteConstellation' en envoyant en paramètre l'id de la constellation à supprimer
        } elseif (isset($_POST['deleteConstellation'])) {
            do_action('deleteConstellation', $_POST['idConstellation']);
        }
        // Si on clique sur le bouton de modification de constellation
        if (isset($_POST['updateConstellation'])) {
            ?>
            <hr>
            <h2>Modifier une constellation</h2>
            <form method="POST" id="doupdateConstellation">
                <input type="hidden" name="idConstellation" id="idConstellation" value="<?php echo $_POST['id']; ?>">
                <label for="nom">Nom de la constellation :</label> <input name="nom" id="nom" type="text" maxlength="150" required value="<?php echo $_POST['nom']; ?>" placeholder="<?php echo $_POST['nom']; ?>"><br>
                <label for="description">Description :</label> <textarea name="description" id="description" maxlength="255"><?php echo $_POST['description']; ?></textarea><br>
                <label for="couleur">Couleur :</label> <input type="color" name="couleur" id="couleur" maxlength="6" value="<?php echo $_POST['couleur']; ?>" placeholder="<?php echo $_POST['couleur']; ?>"><br>
                <label for="photo">Url de la photo :</label> <input name="photo" id="photo" type="text" maxlength="255" value="<?php echo $_POST['photo']; ?>" placeholder="<?php echo $_POST['photo']; ?>"><br>
                <label for="type">Type :</label>
                <select name="type">
                    <option value="Perseus" <?php if($_POST['type'] == 'Perseus'){ echo "selected"; } ?>>Perseus</option>
                    <option value="La Caille" <?php if($_POST['type'] == 'La Caille'){ echo "selected"; } ?>>La Caille</option>
                    <option value="Bayer" <?php if($_POST['type'] == 'Bayer'){ echo "selected"; } ?>>Bayer</option>
                    <option value="Zodiac" <?php if($_POST['type'] == 'Zodiac'){ echo "selected"; } ?>>Zodiac</option>
                    <option value="Hercules" <?php if($_POST['type'] == 'Hercules'){ echo "selected"; } ?>>Hercules</option>
                </select><br>
                <input type="date" name="date" id="date" required <?php if($_POST['date']){ echo "value='".$_POST['date']."'"; } ?>>
                <br>
                <label for="visible">Est-elle visible depuis la Terre :</label> <input name="visible" value="1" type="radio" <?php if($_POST['visible'] == '1'){ echo "checked='checked'"; } ?>> Oui <input name="visible" value="0" type="radio" checked="checked" <?php if($_POST['visible'] == '0'){ echo "checked='checked'"; } ?>> Non <br>
                <input type="submit" name="doupdateConstellation">

            </form>
            <?php
        }
        // Si on envois le formulaire de modification
        if (isset($_POST['doupdateConstellation'])) {
            do_action('updateConstellation', $_POST['idConstellation']);
        }
        
    }

    // Défini la fonction à appeler lors de l'installation de l'extension
    register_activation_hook(__FILE__, array('Constellation','install'));
    // Défini la fonction à appeler lors de la désinstalation de l'extension
    register_deactivation_hook( __FILE__, array('Constellation','uninstall'));

    add_action('admin_menu', 'Constellation_menu');
    // Une action qui s'appelle 'createConstellation', j'envoie un tableau à la function 'createConstellation' de la classe 'Constellation'
    add_action('createConstellation', array('Constellation', 'createConstellation'));
    add_action('deleteConstellation', array('Constellation', 'deleteConstellation'));
    add_action('updateConstellation', array('Constellation', 'updateConstellation'));
     
    // Ajoute un shortcode dont le premier paramètre correspond au nom du shortcode et le second à la fonction à laquelle le shortcode fait appel
    add_shortcode('constellations', 'get_constellations');

    //print_r(wp_get_schedules());

    add_action( 'my_task_hook', 'my_task_function' );

    function my_task_function() {
        $url = plugin_dir_path( __FILE__ ).'cron.txt';
        file_put_contents($url, "Aucune constellation", FILE_TEXT);
        /*
        if(get_constellations('all') != 'Aucune constellation'){
            $retours = "ok";
        }
        else{
            $retours = "Aucune constellation";
        }
        file_put_contents($url, $retours, FILE_TEXT);

        /*
        $query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."collection ORDER BY id DESC", 1);
        if(!file_exists($url)){file_put_contents($url, "FICHIER CREE.");}
        file_put_contents($url, file_put_contents($url).$query."\r\n");
        $constellations = $wpdb->get_results($query);
        file_put_contents($url, "REQUETE PASSEE.");
        $rowcount = $wpdb->num_rows;
        if($rowcount > 0){
            foreach ($constellations as $constellation) {
                file_put_contents($url, "Constellation N°".$constellation->id."\r\n"
                                        ."nom : ".$constellation->nom."\r\n"
                                        ."description : ".$constellation->description."\r\n"
                                        ."couleur : ".$constellation->couleur."\r\n"
                                        ."photo : ".$constellation->photo."\r\n"
                                        ."type : ".$constellation->type."\r\n"
                                        ."date : ".$constellation->date."\r\n"
                                        ."visible : ".$constellation->visible."\r\n"
                                        ."----------"."\r\n"
                                        ."\r\n"
                                        .file_put_contents($url));
            }
        }
        else{
            file_put_contents($url, file_put_contents($url)."Aucune constellation. \r\n");
        }
        */
    }
}
?>