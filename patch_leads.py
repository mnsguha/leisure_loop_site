import sys

file_path = "public/admin/leads.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Add POST handling
post_handler = """requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    if ($pdo) {
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: leads.php");
        exit;
    }
}
"""
content = content.replace("requireAdmin();", post_handler)

# 2. Add 'Actions' header
header_old = """                        <th>Message</th>
                    </tr>"""
header_new = """                        <th>Message</th>
                        <th>Actions</th>
                    </tr>"""
content = content.replace(header_old, header_new)

# 3. Add 'Delete' column
col_old = """                        </td>
                    </tr>
                    <?php endforeach; ?>"""
col_new = """                        </td>
                        <td>
                            <form method="POST" action="leads.php" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$lead['id']; ?>">
                                <button type="submit" class="p-btn-proceed" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 10px; font-size: 0.8rem; width: auto; border-radius: 4px; display: inline-block;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>"""
content = content.replace(col_old, col_new)

# 4. Update colspan
content = content.replace("colspan=\"5\"", "colspan=\"6\"")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("leads.php updated successfully!")
