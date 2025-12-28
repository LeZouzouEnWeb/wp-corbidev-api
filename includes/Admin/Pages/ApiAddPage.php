<?php

namespace CorbiDev\ApiBuilder\Admin\Pages;

use CorbiDev\ApiBuilder\Database\ManifestRepository;
use CorbiDev\ApiBuilder\Admin\ConfirmationModal;
use CorbiDev\ApiBuilder\Admin\MessageModal;

class ApiAddPage
{
    private const ACTION_CREATE = 'corbidev_api_builder_create';

    public static function render(): void
    {
        $model_id = isset($_GET['model_id']) ? (int) $_GET['model_id'] : 0;
        $is_edit  = $model_id > 0;
        $model    = null;
        $version  = null;
        $manifest = null;

        if ($is_edit) {
            $model = ManifestRepository::get_model_with_latest_version($model_id);
            if (!$model || empty($model['version_info'])) {
                echo '<div class="p-8"><div class="max-w-4xl mx-auto bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">';
                echo esc_html__('Impossible de charger le modèle demandé.', 'wp-corbidev-api-new');
                echo '</div></div>';
                return;
            }
            $version  = $model['version_info'];
            $manifest = json_decode($version['manifest_json'] ?? '[]', true) ?: [];
        }

        $name_value        = $is_edit ? ($model['name'] ?? '') : 'API Modèle';
        $slug_value        = $is_edit ? ($model['slug'] ?? '') : 'api-modele';
        $description_value = $is_edit ? ($model['description'] ?? ($manifest['description'] ?? '')) : __('Modèle de plugin enfant...', 'wp-corbidev-api-new');
        $version_value     = $is_edit ? ($version['version'] ?? '1.0.0') : '1.0.0';
        $mode_value        = $is_edit ? ($version['status'] ?? 'edition') : 'edition';
        $expires_value     = $is_edit ? ($version['expires_at'] ?? ($manifest['meta']['expires_at'] ?? '')) : '';
        $permissions       = $manifest['permissions'] ?? ['crud' => ['create', 'read', 'update', 'delete'], 'users' => []];

        // Restauration éventuelle d'une saisie précédente après erreur
        $restored_post = null;
        $current_user  = get_current_user_id();
        if ($current_user) {
            $tmp = get_transient('corbidev_api_builder_form_' . $current_user);
            if (is_array($tmp) && !empty($tmp['action']) && $tmp['action'] === self::ACTION_CREATE) {
                $restored_post = $tmp;
                delete_transient('corbidev_api_builder_form_' . $current_user);
            }
        }

        if ($restored_post) {
            $name_value        = sanitize_text_field(wp_unslash($restored_post['name'] ?? $name_value));
            if (!$is_edit) {
                $slug_value = sanitize_title(wp_unslash($restored_post['slug'] ?? $slug_value));
            }
            $description_value = sanitize_textarea_field(wp_unslash($restored_post['description'] ?? $description_value));

            if ($is_edit) {
                if (isset($restored_post['mode'])) {
                    $mode_value = sanitize_text_field(wp_unslash($restored_post['mode']));
                }
                if (isset($restored_post['expires_at'])) {
                    $expires_value = sanitize_text_field(wp_unslash($restored_post['expires_at']));
                }
            } else {
                if (isset($restored_post['version'])) {
                    $version_value = sanitize_text_field(wp_unslash($restored_post['version']));
                }
            }

            if (!empty($restored_post['permissions_crud']) && is_array($restored_post['permissions_crud'])) {
                $permissions['crud'] = array_map('sanitize_text_field', $restored_post['permissions_crud']);
            }
            if (isset($restored_post['permissions_users'])) {
                $emails = array_filter(array_map('trim', explode(',', sanitize_text_field(wp_unslash($restored_post['permissions_users'])))));
                $permissions['users'] = $emails;
            }
        }

        // Préparation des champs de version
        $version_major       = '';
        $version_minor_patch = '';
        $create_version_major = '1';
        $create_version_minor = '0.0';

        if ($is_edit && preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $version_value, $vm)) {
            // Mode édition : majeure fixe, mineure/patch éditable
            $version_major       = $vm[1];
            $version_minor_patch = $vm[2] . '.' . $vm[3];

            // Si une saisie précédente a été restaurée, on garde la partie mineure/patch tapée
            if ($restored_post && isset($restored_post['version_minor_patch'])) {
                $version_minor_patch = sanitize_text_field(wp_unslash($restored_post['version_minor_patch']));
            }
        } else {
            // Mode création : majeure + mineure/patch séparées
            if (preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $version_value, $vmc)) {
                $create_version_major = $vmc[1];
                $create_version_minor = $vmc[2] . '.' . $vmc[3];
            }

            if ($restored_post) {
                if (isset($restored_post['version_major'])) {
                    $create_version_major = sanitize_text_field(wp_unslash($restored_post['version_major']));
                }
                if (isset($restored_post['version_minor_patch'])) {
                    $create_version_minor = sanitize_text_field(wp_unslash($restored_post['version_minor_patch']));
                } elseif (isset($restored_post['version']) && preg_match('/^(\d+)\.(\d+)\.(\d+)$/', sanitize_text_field(wp_unslash($restored_post['version'])), $vmr)) {
                    $create_version_major = $vmr[1];
                    $create_version_minor = $vmr[2] . '.' . $vmr[3];
                }
            }
        }

        echo '<div class="p-8">';

        // Header modern avec fond dégradé
        echo '<div class="max-w-4xl mx-auto mb-6">';
        echo '<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 flex items-center justify-between gap-4">';
        echo '<div>';
        if ($is_edit) {
            echo '<h1 class="text-2xl font-bold text-white">' . esc_html__('Modifier une API', 'wp-corbidev-api-new') . '</h1>';
            echo '<p class="text-blue-100 mt-1 text-sm">' . esc_html__('Ajustez les paramètres de votre modèle. Le slug et la base de la version ne peuvent pas être modifiés.', 'wp-corbidev-api-new') . '</p>';
        } else {
            echo '<h1 class="text-2xl font-bold text-white">' . esc_html__('Créer une nouvelle API', 'wp-corbidev-api-new') . '</h1>';
            echo '<p class="text-blue-100 mt-1 text-sm">' . esc_html__('Définissez les informations principales de votre modèle d\'API. Le mode sera créé en édition.', 'wp-corbidev-api-new') . '</p>';
        }
        echo '</div>';
        echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-blue-100 border border-white/20">';
        if ($is_edit) {
            echo esc_html(sprintf(__('Mode actuel : %s', 'wp-corbidev-api-new'), $mode_value));
        } else {
            echo esc_html__('Mode : En édition', 'wp-corbidev-api-new');
        }
        echo '</span>';
        echo '</div>';
        echo '</div>';

        // Message d'erreur sous le titre principal (via MessageModal)
        $error_message = '';
        if (!empty($_GET['error'])) {
            $error_message = rawurldecode(sanitize_text_field(wp_unslash($_GET['error'])));
        }
        if ($error_message !== '') {
            MessageModal::render(
                'cv-api-error-banner',
                $error_message,
                MessageModal::TYPE_ERREUR,
                [
                    'title'       => '',
                    'dismissible' => true,
                ]
            );
            MessageModal::print_global_assets();
        }

        // Carte formulaire
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="max-w-4xl mx-auto space-y-6 bg-white p-6 rounded-xl shadow">';
        wp_nonce_field(self::ACTION_CREATE);
        echo '<input type="hidden" name="action" value="' . esc_attr(self::ACTION_CREATE) . '">';
        if ($is_edit) {
            echo '<input type="hidden" name="model_id" value="' . (int) $model_id . '">';
            echo '<input type="hidden" name="version_id" value="' . (int) ($version['id'] ?? 0) . '">';
            echo '<input type="hidden" name="original_version" value="' . esc_attr($version_value) . '">';
            echo '<input type="hidden" name="original_slug" value="' . esc_attr($slug_value) . '">';
            echo '<input type="hidden" name="original_status" value="' . esc_attr($mode_value) . '">';
        } else {
            echo '<input type="hidden" name="mode" value="edition">';
        }

        self::render_input('name', __('Nom de l\'API', 'wp-corbidev-api-new'), 'text', $name_value);

        echo '<div><label class="block text-sm font-medium mb-1" for="slug">' . esc_html__('Slug', 'wp-corbidev-api-new') . '</label>';
        $slug_attr = $is_edit ? ' readonly' : '';
        echo '<input type="text" id="slug" name="slug" class="w-full border rounded px-3 py-2 bg-gray-50" value="' . esc_attr($slug_value) . '"' . $slug_attr . '>';
        if ($is_edit) {
            echo '<p class="text-xs text-gray-500 mt-1">' . esc_html__('Le slug ne peut pas être modifié pour une API existante.', 'wp-corbidev-api-new') . '</p>';
        }
        echo '</div>';

        self::render_textarea('description', __('Description', 'wp-corbidev-api-new'), $description_value);

        if ($is_edit && $version_major !== '' && $version_minor_patch !== '') {
            echo '<div>';
            echo '<label class="block text-sm font-medium mb-1" for="version_minor_patch">' . esc_html__('Version', 'wp-corbidev-api-new') . '</label>';
            echo '<div class="flex items-center gap-2">';
            echo '<span class="text-gray-700">' . esc_html($version_major . '.') . '</span>';
            echo '<input type="text" id="version_minor_patch" name="version_minor_patch" class="flex-1 border rounded px-3 py-2" value="' . esc_attr($version_minor_patch) . '">';
            echo '</div>';
            echo '<p class="text-xs text-gray-500 mt-1">' . esc_html__('Vous pouvez modifier uniquement la partie mineure/patch (ex : 0.1, 1.3).', 'wp-corbidev-api-new') . '</p>';
            echo '</div>';
        } else {
            echo '<div>';
            echo '<label class="block text-sm font-medium mb-1" for="version_major">' . esc_html__('Version', 'wp-corbidev-api-new') . '</label>';
            echo '<div class="flex items-center gap-2">';
            echo '<input type="number" min="0" step="1" id="version_major" name="version_major" class="w-20 border rounded px-3 py-2" value="' . esc_attr($create_version_major) . '">';
            echo '<span class="text-gray-700">.</span>';
            echo '<input type="text" id="version_minor_patch" name="version_minor_patch" class="flex-1 border rounded px-3 py-2" value="' . esc_attr($create_version_minor) . '">';
            echo '</div>';
            echo '<p class="text-xs text-gray-500 mt-1">' . esc_html__('Version majeure et mineure/patch (ex : 1 et 0.0).', 'wp-corbidev-api-new') . '</p>';
            echo '</div>';
        }

        if ($is_edit) {
            echo '<div><label class="block text-sm font-medium mb-1" for="mode">' . esc_html__('Mode', 'wp-corbidev-api-new') . '</label>';
            echo '<select name="mode" id="mode" class="w-full border rounded px-3 py-2">';
            $modes = [
                'edition'  => __('En édition', 'wp-corbidev-api-new'),
                'active'   => __('Active', 'wp-corbidev-api-new'),
                'obsolete' => __('Obsolète', 'wp-corbidev-api-new'),
            ];
            foreach ($modes as $value => $label) {
                // Si l'API est déjà obsolète, ne proposer que "Obsolète"
                if ($mode_value === 'obsolete' && $value !== 'obsolete') {
                    continue;
                }
                $selected = $mode_value === $value ? ' selected' : '';
                echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
            }
            echo '</select>';
            echo '<p class="text-xs text-gray-500 mt-1">' . esc_html__('En mode obsolète, une date de péremption est requise.', 'wp-corbidev-api-new') . '</p>';
            echo '</div>';

            echo '<div id="api-expiry-wrapper" class="' . ($mode_value === 'obsolete' ? '' : 'hidden') . '">';
            echo '<label class="block text-sm font-medium mb-1" for="expires_at">' . esc_html__('Date de péremption', 'wp-corbidev-api-new') . '</label>';
            echo '<input type="date" id="expires_at" name="expires_at" class="w-full border rounded px-3 py-2" value="' . esc_attr($expires_value ? substr($expires_value, 0, 10) : '') . '">';
            echo '</div>';
        }

        echo '<div><label class="block text-sm font-medium mb-1">' . esc_html__('Permissions CRUD', 'wp-corbidev-api-new') . '</label>';
        $crud = ['create', 'read', 'update', 'delete'];
        echo '<div class="flex gap-4">';
        foreach ($crud as $perm) {
            $checked = in_array($perm, $permissions['crud'] ?? [], true) ? ' checked' : '';
            echo '<label class="inline-flex items-center gap-1"><input type="checkbox" name="permissions_crud[]" value="' . esc_attr($perm) . '"' . $checked . '>' . esc_html(ucfirst($perm)) . '</label>';
        }
        echo '</div></div>';

        echo '<div><label class="block text-sm font-medium mb-1">' . esc_html__('Utilisateurs autorisés', 'wp-corbidev-api-new') . '</label>';
        $authorized_users = $permissions['users'] ?? [];
        $users_value     = $is_edit ? implode(', ', $authorized_users) : '';

        // Champ caché qui contient la liste finale des emails (pour le back-end)
        echo '<input type="hidden" id="permissions_users" name="permissions_users" value="' . esc_attr($users_value) . '">';

        // Sélecteur d'utilisateurs WordPress
        $all_users = get_users(['orderby' => 'display_name', 'order' => 'ASC']);
        echo '<div class="flex items-center gap-2 mb-2">';
        echo '<select id="available_user" class="flex-1 border rounded px-3 py-2">';
        echo '<option value="">' . esc_html__('Sélectionner un utilisateur…', 'wp-corbidev-api-new') . '</option>';
        foreach ($all_users as $user) {
            $email = $user->user_email;
            $label = $user->display_name ? ($user->display_name . ' (' . $email . ')') : $email;
            $disabled = in_array($email, $authorized_users, true) ? ' disabled' : '';
            echo '<option value="' . esc_attr($email) . '"' . $disabled . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<button type="button" id="add_authorized_user" class="px-3 py-2 text-sm rounded bg-gray-100 border border-gray-300 hover:bg-gray-200">' . esc_html__('Ajouter', 'wp-corbidev-api-new') . '</button>';
        echo '</div>';

        // Liste des utilisateurs déjà autorisés
        echo '<ul id="authorized_users_list" class="mt-2 space-y-1">';
        foreach ($authorized_users as $email) {
            echo '<li class="flex items-center justify-between text-sm bg-gray-50 border rounded px-3 py-1" data-email="' . esc_attr($email) . '">';
            echo '<span>' . esc_html($email) . '</span>';
            echo '<button type="button" class="text-red-600 hover:text-red-800 text-xs authorized-user-remove" data-email="' . esc_attr($email) . '">' . esc_html__('Retirer', 'wp-corbidev-api-new') . '</button>';
            echo '</li>';
        }
        echo '</ul>';

        echo '<p class="text-xs text-gray-500 mt-2">' . esc_html__('Laissez vide pour autoriser selon les capacités WordPress. Sinon, seuls ces utilisateurs pourront remplir cette API.', 'wp-corbidev-api-new') . '</p>';
        echo '</div>';

        echo '<div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">';
        echo '<a href="' . esc_url(admin_url('admin.php?page=corbidev-api-builder')) . '" class="px-4 py-2 text-sm rounded border border-gray-300 text-gray-700 hover:bg-gray-50">' . esc_html__('Annuler', 'wp-corbidev-api-new') . '</a>';
        $submit_label = $is_edit ? __('Mettre à jour l\'API', 'wp-corbidev-api-new') : __('Créer l\'API', 'wp-corbidev-api-new');
        echo '<button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded shadow hover:bg-blue-700 hover:shadow-md transition">' . esc_html($submit_label) . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        // Script léger pour afficher/masquer la date de péremption selon le mode
        echo '<script>document.addEventListener("DOMContentLoaded",function(){var mode=document.getElementById("mode");var wrap=document.getElementById("api-expiry-wrapper");var date=document.getElementById("expires_at");if(mode&&wrap&&date){var toggle=function(){if(mode.value==="obsolete"){wrap.classList.remove("hidden");date.required=true;}else{wrap.classList.add("hidden");date.required=false;}};mode.addEventListener("change",toggle);toggle();}});</script>';

        // Script pour gérer la liste des utilisateurs autorisés (ajout/suppression avec confirmation)
        $js_remove_label  = wp_json_encode(__('Retirer', 'wp-corbidev-api-new'));
        $js_confirm_label = wp_json_encode(__('Voulez-vous vraiment retirer cet utilisateur de la liste des autorisés ?', 'wp-corbidev-api-new'));
        echo '<script>document.addEventListener("DOMContentLoaded",function(){'
            . 'var hidden=document.getElementById("permissions_users");'
            . 'var list=document.getElementById("authorized_users_list");'
            . 'var select=document.getElementById("available_user");'
            . 'var addBtn=document.getElementById("add_authorized_user");'
            . 'if(!hidden||!list||!select||!addBtn){return;}'
            . 'var parseEmails=function(){var value=hidden.value||"";if(!value.trim()){return [];}return value.split(",").map(function(e){return e.trim();}).filter(function(e){return e.length>0;});};'
            . 'var updateHidden=function(arr){hidden.value=arr.join(", ");};'
            . 'var emails=parseEmails();'
            . 'addBtn.addEventListener("click",function(){var email=select.value;if(!email){return;}if(emails.indexOf(email)!==-1){return;}emails.push(email);updateHidden(emails);'
                . 'var li=document.createElement("li");li.className="flex items-center justify-between text-sm bg-gray-50 border rounded px-3 py-1";li.setAttribute("data-email",email);'
                . 'var span=document.createElement("span");span.textContent=email;'
                . 'var btn=document.createElement("button");btn.type="button";btn.className="text-red-600 hover:text-red-800 text-xs authorized-user-remove";btn.setAttribute("data-email",email);'
                . 'btn.textContent=' . $js_remove_label . ';'
                . 'li.appendChild(span);li.appendChild(btn);list.appendChild(li);'
                . 'for(var i=0;i<select.options.length;i++){if(select.options[i].value===email){select.options[i].disabled=true;break;}}'
            . '});'
            . 'list.addEventListener("click",function(e){var target=e.target;if(!(target&&target.classList&&target.classList.contains("authorized-user-remove"))){return;}var email=target.getAttribute("data-email");if(!email){return;}if(!confirm(' . $js_confirm_label . ')){return;}'
                . 'emails=emails.filter(function(v){return v!==email;});updateHidden(emails);'
                . 'var li=target.closest("li");if(li&&li.parentNode){li.parentNode.removeChild(li);}'
                . 'for(var i=0;i<select.options.length;i++){if(select.options[i].value===email){select.options[i].disabled=false;break;}}'
            . '});'
        . '});</script>';

        // Modales de confirmation lorsque l'on passe en mode obsolète / actif (édition uniquement)
        if ($is_edit) {
            // Obsolète
            $obsolete_message = __('Une fois cette API marquée comme obsolète, vous ne pourrez plus revenir en arrière ni modifier sa configuration (hors titres et regex).', 'wp-corbidev-api-new');
            ConfirmationModal::render(
                'cv-api-obsolete-modal',
                __('Passer cette API en obsolète ?', 'wp-corbidev-api-new'),
                $obsolete_message,
                ConfirmationModal::MODE_ALERTE,
                [
                    'description'   => '<span id="cv-api-obsolete-description"></span>',
                    'confirm_label' => __('Continuer', 'wp-corbidev-api-new'),
                    'cancel_label'  => __('Annuler', 'wp-corbidev-api-new'),
                ]
            );

            // Active (même message que sur la liste)
            $activation_message = __('Une fois l’API activée, vous ne pourrez plus modifier ni supprimer les champs existants (hors titres et regex). Vous pourrez uniquement ajouter de nouveaux champs.', 'wp-corbidev-api-new');
            ConfirmationModal::render(
                'cv-api-active-modal',
                __('Activer cette API ?', 'wp-corbidev-api-new'),
                $activation_message,
                ConfirmationModal::MODE_ALERTE,
                [
                    'description'   => __('Vous êtes sur le point d\'activer cette API depuis l\'écran de modification.', 'wp-corbidev-api-new'),
                    'confirm_label' => __('Continuer', 'wp-corbidev-api-new'),
                    'cancel_label'  => __('Annuler', 'wp-corbidev-api-new'),
                ]
            );

            ConfirmationModal::print_global_script();

            echo '<script>document.addEventListener("DOMContentLoaded",function(){'
                . 'var mode=document.getElementById("mode");'
                . 'var obsoleteModal=document.getElementById("cv-api-obsolete-modal");'
                . 'var activeModal=document.getElementById("cv-api-active-modal");'
                . 'if(!mode||!obsoleteModal||!activeModal){return;}'
                . 'var form=mode.form;'
                . 'var originalStatusInput=document.querySelector("input[name=\\"original_status\\"]");'
                . 'var originalStatus=originalStatusInput?originalStatusInput.value:"";'
                . 'var prev=mode.value;'
                . 'var confirmedObsolete=false;'
                . 'var confirmedActive=false;'
                . 'var confirmObsolete=obsoleteModal.querySelector("[data-modal-confirm=\\"cv-api-obsolete-modal\\"]");'
                . 'var cancelObsolete=obsoleteModal.querySelector("[data-modal-cancel=\\"cv-api-obsolete-modal\\"]");'
                . 'var confirmActive=activeModal.querySelector("[data-modal-confirm=\\"cv-api-active-modal\\"]");'
                . 'var cancelActive=activeModal.querySelector("[data-modal-cancel=\\"cv-api-active-modal\\"]");'
                . 'var desc=document.getElementById("cv-api-obsolete-description");'
                . 'if(!confirmObsolete||!cancelObsolete||!confirmActive||!cancelActive){return;}'
                . 'var openModal=function(m){m.classList.remove("hidden");m.classList.add("flex");};'
                . 'var closeModal=function(m){m.classList.add("hidden");m.classList.remove("flex");};'
                . 'mode.addEventListener("change",function(){var newVal=mode.value;'
                . 'if(newVal===prev){return;}'
                . 'if(newVal==="obsolete"){if(desc){desc.textContent="' . esc_js(__('Vous êtes sur le point de passer cette API en mode obsolète.', 'wp-corbidev-api-new')) . '";}openModal(obsoleteModal);return;}'
                . 'if(newVal==="active"){openModal(activeModal);return;}'
                . 'prev=newVal;'
                . '});'
                . 'cancelObsolete.addEventListener("click",function(e){e.preventDefault();closeModal(obsoleteModal);mode.value=prev;mode.dispatchEvent(new Event("change",{bubbles:true}));});'
                . 'confirmObsolete.addEventListener("click",function(e){e.preventDefault();closeModal(obsoleteModal);prev="obsolete";confirmedObsolete=true;mode.value="obsolete";mode.dispatchEvent(new Event("change",{bubbles:true}));});'
                . 'cancelActive.addEventListener("click",function(e){e.preventDefault();closeModal(activeModal);mode.value=prev;mode.dispatchEvent(new Event("change",{bubbles:true}));});'
                . 'confirmActive.addEventListener("click",function(e){e.preventDefault();closeModal(activeModal);prev="active";confirmedActive=true;mode.value="active";mode.dispatchEvent(new Event("change",{bubbles:true}));});'
                . 'if(form){form.addEventListener("submit",function(e){var current=mode.value;'
                . 'if(current==="obsolete" && originalStatus!=="obsolete" && !confirmedObsolete){e.preventDefault();if(desc){desc.textContent="' . esc_js(__('Vous êtes sur le point de passer cette API en mode obsolète.', 'wp-corbidev-api-new')) . '";}openModal(obsoleteModal);return;}'
                . 'if(current==="active" && originalStatus!=="active" && !confirmedActive){e.preventDefault();openModal(activeModal);return;}'
                . '});}
                });</script>';
        }
    }

    public static function handle_create(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Accès refusé.', 'wp-corbidev-api-new'));
        }

        check_admin_referer(self::ACTION_CREATE);

        $model_id    = isset($_POST['model_id']) ? (int) $_POST['model_id'] : 0;
        $version_id  = isset($_POST['version_id']) ? (int) $_POST['version_id'] : 0;
        $is_edit     = $model_id > 0 && $version_id > 0;

        $name        = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
        $slug        = sanitize_title(wp_unslash($_POST['slug'] ?? ''));
        $description = sanitize_textarea_field(wp_unslash($_POST['description'] ?? ''));
        $version     = '1.0.0';
        $mode        = $is_edit ? sanitize_text_field(wp_unslash($_POST['mode'] ?? 'edition')) : 'edition';
        $expires_raw = $is_edit ? sanitize_text_field(wp_unslash($_POST['expires_at'] ?? '')) : '';
        $permissions_crud  = array_map('sanitize_text_field', $_POST['permissions_crud'] ?? []);
        $permissions_users = array_filter(array_map('trim', explode(',', sanitize_text_field(wp_unslash($_POST['permissions_users'] ?? '')))));

        // Construction/édition de la version à partir des champs séparés
        $original_version = '';
        if ($is_edit) {
            $original_version = sanitize_text_field(wp_unslash($_POST['original_version'] ?? ''));
            $minor_patch      = sanitize_text_field(wp_unslash($_POST['version_minor_patch'] ?? ''));

            if ($minor_patch !== '') {
                if (!preg_match('/^(\d+)\.(\d+)$/', $minor_patch, $mp) || !preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $original_version, $ov)) {
                    self::redirect_with_error(__('La version doit être au format X.Y.Z (ex : 1.0.0).', 'wp-corbidev-api-new'));
                }
                $version = $ov[1] . '.' . $mp[1] . '.' . $mp[2];
            } else {
                // Si rien n'est saisi, on garde la version d'origine
                $version = $original_version ?: $version;
            }
        } else {
            // Création : utiliser version_major + version_minor_patch
            $major_input = sanitize_text_field(wp_unslash($_POST['version_major'] ?? ''));
            $minor_input = sanitize_text_field(wp_unslash($_POST['version_minor_patch'] ?? ''));

            if ($major_input === '') {
                $major_input = '1';
            }
            if ($minor_input === '') {
                $minor_input = '0.0';
            }

            if (!preg_match('/^\d+$/', $major_input) || !preg_match('/^(\d+)\.(\d+)$/', $minor_input, $mp_create)) {
                self::redirect_with_error(__('La version doit être au format X.Y.Z (ex : 1.0.0).', 'wp-corbidev-api-new'));
            }

            $version = (int) $major_input . '.' . $mp_create[1] . '.' . $mp_create[2];
        }

        if (!$name || !$slug) {
            self::redirect_with_error(__('Le nom et le slug sont obligatoires.', 'wp-corbidev-api-new'));
        }

        // Validation stricte du format de version complet X.Y.Z
        if (!preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $version, $new_parts)) {
            self::redirect_with_error(__('La version doit être au format X.Y.Z (ex : 1.0.0).', 'wp-corbidev-api-new'));
        }
        $new_major = (int) $new_parts[1];

        // En création, si le slug ne contient pas la version majeure (v1, v2, ...),
        // on le corrige automatiquement au lieu d'afficher une erreur.
        if (!$is_edit && stripos($slug, 'v' . $new_major) === false) {
            $slug = sanitize_title($slug . '-v' . $new_major);
        }

        if (!$is_edit && ManifestRepository::slug_exists($slug)) {
            self::redirect_with_error(__('Ce slug existe déjà.', 'wp-corbidev-api-new'));
        }

        if ($is_edit) {
            $original_slug   = sanitize_title(wp_unslash($_POST['original_slug'] ?? ''));
            $original_status = sanitize_text_field(wp_unslash($_POST['original_status'] ?? ''));

            if ($original_status === 'obsolete' && $mode !== 'obsolete') {
                self::redirect_with_error(__('Vous ne pouvez pas changer le mode d\'une API déjà obsolète.', 'wp-corbidev-api-new'));
            }

            // Interdiction de changer la base de version (majeur)
            if ($original_version && preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $original_version, $orig_parts)) {
                $orig_major = (int) $orig_parts[1];
                if ($orig_major !== $new_major) {
                    self::redirect_with_error(__('Vous ne pouvez pas changer la version majeure (ex : 1.x.x -> 2.x.x).', 'wp-corbidev-api-new'));
                }
            }

            if ($slug !== $original_slug) {
                // Ignorer toute tentative de changement de slug
                $slug = $original_slug;
            }

            if ($mode === 'obsolete' && $expires_raw === '') {
                self::redirect_with_error(__('Une date de péremption est requise pour une API obsolète.', 'wp-corbidev-api-new'));
            }
        }

        $modules = json_decode(self::default_modules_json(), true);

        $now_iso = current_time('Y-m-d\TH:i:s\Z', true);

        $manifest = [
            'name'        => $name,
            'description' => $description,
            'version'     => $version,
            'mode'        => $mode,
            'modules'     => $modules,
            'permissions' => [
                'crud'  => $permissions_crud ?: ['create', 'read', 'update', 'delete'],
                'users' => $permissions_users,
            ],
            'meta' => [
                'created_at' => $now_iso,
                'updated_at' => $now_iso,
                'deprecated' => false,
                'expires_at' => null,
            ],
        ];

        if ($is_edit) {
            global $wpdb;
            $models_table   = ManifestRepository::get_models_table();
            $versions_table = ManifestRepository::get_versions_table();

            $row = $wpdb->get_row(
                $wpdb->prepare("SELECT manifest_json FROM {$versions_table} WHERE id = %d", $version_id),
                ARRAY_A
            );

            if (!$row) {
                self::redirect_with_error(__('Version introuvable.', 'wp-corbidev-api-new'));
            }

            $existing_manifest = json_decode($row['manifest_json'] ?? '[]', true) ?: [];

            $existing_manifest['name']        = $manifest['name'];
            $existing_manifest['description'] = $manifest['description'];
            $existing_manifest['version']     = $manifest['version'];
            $existing_manifest['mode']        = $manifest['mode'];
            $existing_manifest['permissions'] = $manifest['permissions'];

            if (!isset($existing_manifest['meta']) || !is_array($existing_manifest['meta'])) {
                $existing_manifest['meta'] = [];
            }
            $existing_manifest['meta']['updated_at'] = $now_iso;

            if ($mode === 'obsolete') {
                $existing_manifest['meta']['deprecated'] = true;
                $existing_manifest['meta']['expires_at'] = $expires_raw;
            } else {
                $existing_manifest['meta']['deprecated'] = false;
                $existing_manifest['meta']['expires_at'] = null;
            }

            $wpdb->update(
                $models_table,
                [
                    'name'        => $name,
                    'description' => $description,
                ],
                ['id' => $model_id],
                ['%s', '%s'],
                ['%d']
            );

            $wpdb->update(
                $versions_table,
                [
                    'version'       => $version,
                    'status'        => $mode,
                    'expires_at'    => $expires_raw ?: null,
                    'updated_at'    => current_time('mysql'),
                    'manifest_json' => wp_json_encode($existing_manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
                ['id' => $version_id],
                ['%s', '%s', '%s', '%s', '%s'],
                ['%d']
            );

            wp_safe_redirect(add_query_arg(['page' => 'corbidev-api-builder'], admin_url('admin.php')));
        } else {
            try {
                ManifestRepository::insert_model_with_version(
                    [
                        'slug'        => $slug,
                        'name'        => $name,
                        'description' => $description,
                        'version'     => $version,
                        'status'      => $mode,
                    ],
                    $manifest
                );
            } catch (\Throwable $e) {
                self::redirect_with_error($e->getMessage());
            }

            wp_safe_redirect(add_query_arg(['page' => 'corbidev-api-builder', 'created' => 1], admin_url('admin.php')));
        }
        exit;
    }

    private static function render_input(string $name, string $label, string $type, string $value = ''): void
    {
        echo '<div><label class="block text-sm font-medium mb-1" for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" class="w-full border rounded px-3 py-2" value="' . esc_attr($value) . '">';
        echo '</div>';
    }

    private static function render_textarea(string $name, string $label, string $value = ''): void
    {
        echo '<div><label class="block text-sm font-medium mb-1" for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<textarea id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" rows="3" class="w-full border rounded px-3 py-2">' . esc_textarea($value) . '</textarea>';
        echo '</div>';
    }

    private static function default_modules_json(): string
    {
        $template = [
            [
                'name' => 'modele',
                'tabs' => [
                    [
                        'name'   => 'Exemple',
                        'fields' => [
                            [
                                'key'        => 'example_field',
                                'label'      => 'Champ exemple',
                                'type'       => 'input',
                                'validation' => ['required' => false],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return wp_json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private static function redirect_with_error(string $message): void
    {
        // Par défaut, retour à la liste, mais si on vient du formulaire
        // de création/édition, on renvoie vers cet écran pour éviter de
        // refaire tout le parcours.
        $page   = 'corbidev-api-builder';
        $args   = [];

        if (!empty($_POST['action']) && $_POST['action'] === self::ACTION_CREATE) {
            $page = 'corbidev-api-builder-add';

            // Sauvegarde temporaire de la saisie pour la restaurer après l'erreur
            $current_user = get_current_user_id();
            if ($current_user) {
                set_transient('corbidev_api_builder_form_' . $current_user, wp_unslash($_POST), 5 * MINUTE_IN_SECONDS);
            }

            if (!empty($_POST['model_id'])) {
                $args['model_id'] = (int) $_POST['model_id'];
            }
        }

        $args['page']  = $page;
        $args['error'] = rawurlencode($message);

        wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
        exit;
    }
}