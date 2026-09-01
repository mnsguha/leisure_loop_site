import sys

file_path = "public/admin/leads.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update POST handler
old_post_handler = """if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    if ($pdo) {
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: leads.php");
        exit;
    }
}"""

new_post_handler = """if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['lead_ids'])) {
    if ($pdo) {
        $ids = $_POST['lead_ids'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        header("Location: leads.php");
        exit;
    }
}"""
content = content.replace(old_post_handler, new_post_handler)

# 2. Add Top Buttons and Form Start
old_table_start = """            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>"""

new_table_start = """            <form method="POST" action="leads.php" id="bulkDeleteForm">
                <input type="hidden" name="action" value="bulk_delete">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1rem;">
                    <div></div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" id="btnCancel" class="p-btn-proceed" style="background: transparent; color: var(--text-muted); border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; font-size: 0.9rem; width: auto; border-radius: 4px; display: none; cursor: pointer;">Cancel</button>
                        <button type="submit" id="btnBulkDelete" class="p-btn-proceed" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 8px 16px; font-size: 0.9rem; width: auto; border-radius: 4px; display: none; cursor: pointer;" onclick="return confirm('Are you sure you want to delete selected inquiries?');">Delete Selected</button>
                    </div>
                </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAll" style="accent-color: var(--gold); cursor: pointer; width: 16px; height: 16px;">
                        </th>
                        <th>Date</th>"""
content = content.replace(old_table_start, new_table_start)

# 3. Remove Actions Header
old_actions_header = """                        <th>Message</th>
                        <th>Actions</th>
                    </tr>"""
new_actions_header = """                        <th>Message</th>
                    </tr>"""
content = content.replace(old_actions_header, new_actions_header)


# 4. Update Table Row Checkboxes & Remove Delete Button
old_td_start = """                    <tr>
                        <td style="font-size: 0.85rem; color: var(--text-muted);">"""

new_td_start = """                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="lead_ids[]" value="<?php echo (int)$lead['id']; ?>" class="lead-checkbox" style="accent-color: var(--gold); cursor: pointer; width: 16px; height: 16px;">
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-muted);">"""
content = content.replace(old_td_start, new_td_start)

old_actions_col = """                        <td>
                            <form method="POST" action="leads.php" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$lead['id']; ?>">
                                <button type="submit" class="p-btn-proceed" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 10px; font-size: 0.8rem; width: auto; border-radius: 4px; display: inline-block;">Delete</button>
                            </form>
                        </td>
                    </tr>"""
new_actions_col = """                    </tr>"""
content = content.replace(old_actions_col, new_actions_col)

# 5. Form End & Javascript
old_table_end = """            </table>
        </main>
    </div>
</body>"""
new_table_end = """            </table>
            </form>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.lead-checkbox');
            const btnDelete = document.getElementById('btnBulkDelete');
            const btnCancel = document.getElementById('btnCancel');
            const form = document.getElementById('bulkDeleteForm');

            function updateButtons() {
                const checkedCount = document.querySelectorAll('.lead-checkbox:checked').length;
                if (checkedCount > 0) {
                    btnDelete.style.display = 'inline-block';
                    btnCancel.style.display = 'inline-block';
                    btnDelete.innerText = `Delete (${checkedCount})`;
                } else {
                    btnDelete.style.display = 'none';
                    btnCancel.style.display = 'none';
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateButtons();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = document.querySelectorAll('.lead-checkbox:checked').length === checkboxes.length;
                    selectAll.checked = allChecked && checkboxes.length > 0;
                    updateButtons();
                });
            });

            if (btnCancel) {
                btnCancel.addEventListener('click', function() {
                    checkboxes.forEach(cb => cb.checked = false);
                    if (selectAll) selectAll.checked = false;
                    updateButtons();
                });
            }
        });
    </script>
</body>"""
content = content.replace(old_table_end, new_table_end)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Bulk delete implemented successfully!")
