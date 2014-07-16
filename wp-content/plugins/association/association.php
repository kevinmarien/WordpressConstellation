<?php
/*
Plugin Name: Association
Description: Liste des membres de l'association
Version: 0.1
License: GPL
Author: Kevin Marien, Jonathan Esedji
*/

if ( !class_exists("Association") )
{
    class Association
    {
       public function install() {
            }
        // Supprime tous les champs de la table wp_options dont option_name commende par 'membre_'
        public function uninstall() {
            global $wpdb;
            $query = "DELETE * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'membre_%';";
            $wpdb->query($query);
        }
    }
    
    function asso_menu() {
       add_options_page( 'Liste des membres', 'Liste des membres', 'manage_options', __FILE__, 'asso_admin' );
    }

    function get_membres(){
        // Récupère tous les membres de l'association
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'membre%%';", 1);
        //echo "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'membre%';";
        $membres = $wpdb->get_results($query);

        $rowcount = $wpdb->num_rows;

        if($rowcount > 0){ 
            ?>

            <h2><?php echo $title;?></h2>
            <table class="wp-list-table widefat media" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="nom_membre" class="" style="">
                            <p>Membre</p>
                        </th>
                        <?php if((current_user_can('manage_options')) && (is_admin()) ){ ?>
                            <th scope="col" id="action" class="" style="">
                                <p>Action</p>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="the-list">
                <?php
                    foreach ($membres as $membre) { ?>
                        <tr id="<?php echo $membre->id; ?>" class="alternate author-self status-inherit" valign="top">
                           <td class="column-icon media-icon">
                                <p><?php echo $membre->option_value; ?></p>
                            </td>
                            <?php if((current_user_can('manage_options')) && (is_admin()) ){ ?>
                                <td class="title column-title">
                                    <form id="update_membre" method="post">
                                        <input type="hidden" name="option_to_update_id" id="option_to_update_id" value="<?php echo $membre->option_name; ?>">
                                        <input type="hidden" name="option_to_update_value" id="option_to_update_value" value="<?php echo $membre->option_value; ?>">
                                        <input type="submit" name="update_asso" id="update_asso" value="Modifier">
                                    </form>
                                    <form id="delete_membre" method="post">
                                        <input type="hidden" name="option_to_delete" id="option_to_delete" value="<?php echo $membre->option_name; ?>">
                                        <input type="submit" name="delete_asso" id="delete_asso" value="Supprimer">
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
        }
    }
    
    function asso_admin() {
        global $title;
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( "Vous n'avez pas les permissions pour accéder à cette page." ) );
        }
        get_membres();
        ?>
        <hr>
        <h2>Ajouter  un nouveau membre</h2>
        <form method="POST" id="addasso">
            <label for="option_value">Membre : </label><input name="option_value" id="option_value" type="text" required>
            <input type="submit" name="submit_asso">
        </form>
        <?php
        if (isset($_POST['submit_asso'])) {
            // Récupère le prochain ID
            $get_next_id = mysql_query("SHOW TABLE STATUS LIKE '".$wpdb->prefix."options'");
            $row = mysql_fetch_array($get_next_id);
            $nextId = hash('crc32', 'membre_'.$_POST['option_value']);

            // add_option est la fonction WP permettant d'ajouter des données dans la table wp_option
            if(add_option('membre_'.$nextId, $_POST['option_value'])){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
        }
        if (isset($_POST['delete_asso'])) {
            // appel de la fonction WP 'delete_option' qui permet de supprimer dans la table wp_option
            if(delete_option($_POST['option_to_delete'])){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
            else{
                echo "Impossible d'effectué la suppression.";
            }
        }
        // Affiche le formulaire de modification
        if (isset($_POST['update_asso'])) {
            ?>
            <h2>Modification d'un membre</h2>
            <form method="POST" id="doupdateasso">
                <label for="new_option">Membre : </label><input name="new_option" id="new_option" type="text" placeholder="<?php echo $_POST['option_to_update_value']; ?>">
                <input type="hidden" name="option_to_update" id="option_to_update" value="<?php echo $_POST['option_to_update_id']; ?>">
                <input type="submit" name="do_update_asso">
            </form>
            <?php
        }
        // Effectue la modification
        if (isset($_POST['do_update_asso'])) {
            // appel la fonction WP 'update_option' qui permet de modifier les données de la table wp_option
            if(update_option($_POST['option_to_update'], $_POST['new_option'])){
                ?>
                <script type="text/javascript">
                    document.location.reload();
                </script>
                <?php
            }
            else{
                echo "Impossible d'effectué la suppression.";
            }
        }
        
    }
    register_activation_hook(__FILE__, array('asso','install'));
    register_deactivation_hook( __FILE__, array('asso','uninstall'));
    add_action('admin_menu', 'asso_menu');
    add_action('createasso', 'create_asso');
    add_action('updatemembre', 'update_membre');
    add_shortcode('liste_des_membres', 'get_membres');
}

?>