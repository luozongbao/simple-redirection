<div class="wrap">
    <h1>Simple Redirection</h1>
    
    <!-- Create New Slug Form -->
    <div class="wsr-form-container">
        <h2>Create New Redirect</h2>
        <form id="wsr-create-form">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="slug">Slug</label></th>
                    <td>
                        <input type="text" id="slug" name="slug" class="regular-text" placeholder="my-custom-slug or path/to/page" required>
                        <p class="description">Enter the slug (supports "/" for nested paths)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="target_url">Target URL</label></th>
                    <td>
                        <input type="url" id="target_url" name="target_url" class="regular-text" placeholder="https://example.com" required>
                        <p class="description">The URL to redirect to</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Create Redirect">
            </p>
        </form>
    </div>
    
    <!-- Redirects List -->
    <div class="wsr-list-container">
        <h2>Existing Redirects</h2>
        
        <?php if (empty($slugs)): ?>
            <p>No redirects created yet.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Slug</th>
                        <th>Target URL</th>
                        <th>Count</th>
                        <th>Last Called</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slugs as $slug): ?>
                        <tr data-id="<?php echo esc_attr($slug->id); ?>">
                            <td>
                                <strong><?php echo esc_html($slug->slug); ?></strong>
                                <div class="row-actions">
                                    <span class="copy-snippet">
                                        <a href="#" class="copy-slug" data-slug="<?php echo esc_attr(home_url('/' . $slug->slug)); ?>">Copy Full Link</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo esc_url($slug->target_url); ?>" target="_blank">
                                    <?php echo esc_html($slug->target_url); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($slug->redirect_count); ?></td>
                            <td>
                                <?php 
                                if ($slug->last_called) {
                                    echo esc_html(date('Y-m-d H:i:s', strtotime($slug->last_called)));
                                } else {
                                    echo 'Never';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($slug->status); ?>">
                                    <?php echo esc_html(ucfirst($slug->status)); ?>
                                </span>
                            </td>
                            <td>
                                <button class="button button-small edit-slug" data-id="<?php echo esc_attr($slug->id); ?>">Edit</button>
                                <button class="button button-small toggle-status" data-id="<?php echo esc_attr($slug->id); ?>" data-status="<?php echo esc_attr($slug->status); ?>">
                                    <?php echo $slug->status === 'active' ? 'Disable' : 'Enable'; ?>
                                </button>
                                <button class="button button-small test-redirect" data-id="<?php echo esc_attr($slug->id); ?>">Test</button>
                                <button class="button button-small button-link-delete delete-slug" data-id="<?php echo esc_attr($slug->id); ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Modal -->
<div id="wsr-edit-modal" class="wsr-modal" style="display: none;">
    <div class="wsr-modal-content">
        <div class="wsr-modal-header">
            <h2>Edit Redirect</h2>
            <span class="wsr-modal-close">&times;</span>
        </div>
        <div class="wsr-modal-body">
            <form id="wsr-edit-form">
                <input type="hidden" id="edit-id" name="id">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="edit-slug">Slug</label></th>
                        <td>
                            <input type="text" id="edit-slug" name="slug" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="edit-target_url">Target URL</label></th>
                        <td>
                            <input type="url" id="edit-target_url" name="target_url" class="regular-text" required>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Update Redirect">
                    <button type="button" class="button wsr-modal-close">Cancel</button>
                </p>
            </form>
        </div>
    </div>
</div>

<!-- Copy notification -->
<div id="wsr-copy-notification" class="notice notice-success" style="display: none;">
    <p>Full link copied to clipboard!</p>
</div>