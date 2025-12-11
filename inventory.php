<?php require_once 'inventory_end.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Tuktukan Health Center</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="inventory_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- INTERNAL CSS TO FIX BUTTONS IMMEDIATELY --- */
        .action-btn-group {
            display: flex;
            gap: 5px; /* Space between buttons */
            justify-content: center;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            color: white;
            font-size: 14px;
        }
        .btn-edit { background-color: #ffc107; color: #000; } /* Yellow */
        .btn-edit:hover { background-color: #e0a800; }
        
        .btn-delete { background-color: #dc3545; } /* Red */
        .btn-delete:hover { background-color: #c82333; }

        .btn-dispense { background-color: #17a2b8; width: auto; padding: 0 10px; } /* Teal */
        .btn-request { background-color: #28a745; width: auto; padding: 0 10px; } /* Green */
        
        /* Ensure table aligns text vertically */
        .inventory-table td { vertical-align: middle; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="Tuktukan_Logo.png" alt="Logo" class="sidebar-logo">
                <h3>Barangay Tuktukan Inventory System</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="active"><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </aside>

        <div class="main-content">
            
            <header>
                <h2>Inventory Management</h2>
                
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-info">
                        <span class="user-name"><?php echo $user_name; ?></span>
                        <span class="user-role"><?php echo $user_role; ?></span>
                    </div>
                    <div class="profile-icon"><?php echo $user_initial; ?></div>
                    
                    <div id="userDropdown" class="dropdown-menu">
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                    </div>
                </div>
            </header>

            <main>
                <?php echo $message; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'dispensed'): ?>
                    <div class="alert success">✅ Medicine dispensed successfully!</div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'requested'): ?>
                    <div class="alert success" style="background:#ffc107; color:black;">⏳ Request sent to Admin!</div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                    <div class="alert success" style="background:#dc3545; color:white;">🗑️ Item deleted successfully.</div>
                <?php endif; ?>

                <?php if ($user_role === 'ADMIN'): ?>
                    <div style="margin-bottom: 20px; text-align: right;">
                        <button class="btn-add" onclick="openModal()">
                            <i class="fas fa-plus"></i> Add New Item
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory_list as $item): ?>
                                <tr>
                                    <td><?php echo $item['item_name']; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td style="font-weight: bold; font-size: 1.1em;"><?php echo $item['quantity']; ?></td>
                                    <td>
                                        <?php 
                                            echo $item['expiry_date']; 
                                            if ($item['expiry_date'] < date('Y-m-d')) echo " <span class='expired'>(Expired)</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($item['quantity'] == 0): ?>
                                            <span style="color:red; font-weight:bold;">Out of Stock</span>
                                        <?php elseif ($item['quantity'] < 50): ?>
                                            <span style="color:orange; font-weight:bold;">Low Stock</span>
                                        <?php else: ?>
                                            <span style="color:green;">Good</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-btn-group">
                                            
                                            <?php if ($user_role === 'ADMIN'): ?>
                                                <button class="btn-icon btn-edit" title="Edit Item"
                                                    onclick="openEditModal('<?php echo $item['id']; ?>', '<?php echo $item['item_name']; ?>', '<?php echo $item['category']; ?>', '<?php echo $item['quantity']; ?>', '<?php echo $item['expiry_date']; ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form method="POST" action="inventory.php" style="margin:0;">
                                                    <input type="hidden" name="action" value="delete_item">
                                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="item_name" value="<?php echo $item['item_name']; ?>">
                                                    <button type="submit" class="btn-icon btn-delete" title="Delete Item" onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>

                                            <?php else: ?>
                                                <button class="btn-icon btn-dispense" title="Dispense Stock"
                                                    onclick="openDispenseModal('<?php echo $item['id']; ?>', '<?php echo $item['item_name']; ?>', '<?php echo $item['quantity']; ?>')">
                                                    <i class="fas fa-minus-circle"></i> Dispense
                                                </button>

                                                <button class="btn-icon btn-request" title="Request Stock"
                                                    onclick="openRequestModal('<?php echo $item['id']; ?>', '<?php echo $item['item_name']; ?>')">
                                                    <i class="fas fa-cart-plus"></i> Request
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="addModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Add New Item</h2>
                        <form method="POST" action="inventory.php">
                            <input type="hidden" name="action" value="add_item">
                            <div class="form-group"><label>Item Name</label><input type="text" name="item_name" required></div>
                            <div class="form-group"><label>Category</label><select name="category"><option>Medicine</option><option>Supplies</option><option>Equipment</option></select></div>
                            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" required></div>
                            <div class="form-group"><label>Expiry</label><input type="date" name="expiry_date" required></div>
                            <button type="submit" class="btn-submit">Add Item</button>
                        </form>
                    </div>
                </div>

                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeEditModal()">&times;</span>
                        <h2>Edit Item</h2>
                        <form method="POST" action="inventory.php">
                            <input type="hidden" name="action" value="update_item">
                            <input type="hidden" name="item_id" id="edit_id">
                            <div class="form-group"><label>Item Name</label><input type="text" name="item_name" id="edit_name" required></div>
                            <div class="form-group"><label>Category</label><select name="category" id="edit_category"><option>Medicine</option><option>Supplies</option><option>Equipment</option></select></div>
                            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" id="edit_quantity" required></div>
                            <div class="form-group"><label>Expiry</label><input type="date" name="expiry_date" id="edit_expiry" required></div>
                            <button type="submit" class="btn-submit">Update</button>
                        </form>
                    </div>
                </div>

                <div id="dispenseModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeDispenseModal()">&times;</span>
                        <h2 style="color:#17a2b8;">Dispense Medicine</h2>
                        <form method="POST" action="inventory.php">
                            <input type="hidden" name="action" value="dispense_item">
                            <input type="hidden" name="item_id" id="dispense_id">
                            <div class="form-group"><label>Item Name</label><input type="text" id="dispense_name" readonly style="background:#eee;"></div>
                            <div class="form-group"><label>Current Stock</label><input type="text" id="dispense_current_qty" readonly style="background:#eee;"></div>
                            <div class="form-group"><label>Quantity to Dispense</label><input type="number" name="quantity_to_dispense" min="1" required></div>
                            <button type="submit" class="btn-submit" style="background:#17a2b8;">Confirm Dispense</button>
                        </form>
                    </div>
                </div>

                <div id="requestModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeRequestModal()">&times;</span>
                        <h2 style="color:#28a745;">Request Stock</h2>
                        <form method="POST" action="inventory.php">
                            <input type="hidden" name="action" value="request_item">
                            <input type="hidden" name="item_id" id="request_id">
                            <div class="form-group"><label>Item Name</label><input type="text" id="request_name" readonly style="background:#eee;"></div>
                            <div class="form-group"><label>Quantity Needed</label><input type="number" name="quantity_needed" min="1" required></div>
                            <button type="submit" class="btn-submit" style="background:#28a745;">Send Request</button>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="inventory_script.js"></script>
</body>
</html>