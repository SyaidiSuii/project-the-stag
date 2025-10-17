@extends('layouts.admin')

@section('title', 'Table Management')
@section('page-title', 'Table Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section" id="layout-section" style="display: block;">
      <div class="section-header">
        <h2 class="section-title">Table Layout Editor</h2>
        <div class="admin-actions">
            <button class="admin-btn btn-secondary" id="add-vvip-room-btn" aria-label="Add VVIP room">
              <i class="fas fa-crown" aria-hidden="true"></i> Add VVIP Room
            </button>
            <button class="admin-btn btn-secondary" id="add-square-table-btn" aria-label="Add square table">
              <i class="fas fa-plus" aria-hidden="true"></i> Add Square Table
            </button>
            <button class="admin-btn btn-secondary" id="add-rectangle-table-btn" aria-label="Add rectangle table">
              <i class="fas fa-plus" aria-hidden="true"></i> Add Rectangle Table
            </button>
            <button class="admin-btn btn-danger" id="reset-layout-btn" aria-label="Reset layout">
              <i class="fas fa-undo" aria-hidden="true"></i> Reset
            </button>
            <button class="admin-btn btn-primary" id="save-layout-btn" aria-label="Save layout">
              <i class="fas fa-save" aria-hidden="true"></i> Save Layout
            </button>
        </div>
      </div>

      <div class="layout-editor-wrapper">
        <div id="layout-editor-container" role="application" aria-label="Table layout editor" style="width: {{ $layoutSetting->container_width }}px; height: {{ $layoutSetting->container_height }}px;">
          @foreach($tables as $table)
          @php
            $coordinates = $table->coordinates ?? [];
            $x = $coordinates['x'] ?? (30 + (($loop->index % 5) * 110));
            $y = $coordinates['y'] ?? (30 + (floor($loop->index / 5) * 100));

            // Determine size class based on capacity
            if ($table->table_type === 'vip') {
              $sizeClass = 'vvip'; // VVIP always stays same size
              $colorClass = '';
            } else {
              // Size: capacity < 5 = small (rectangle), capacity >= 5 = large (square)
              $sizeClass = $table->capacity < 5 ? 'rectangle' : 'square';

              // Color: based on table type
              $colorClass = $table->table_type === 'outdoor' ? 'outdoor-color' : 'indoor-color';
            }

            $tableClass = trim($sizeClass . ' ' . $colorClass);
          @endphp
          <div class="layout-table {{ $tableClass }}" 
               id="table-{{ $table->id }}" 
               data-table-id="{{ $table->id }}"
               style="left: {{ $x }}px; top: {{ $y }}px;">
            {{ $table->table_number }}
            <span class="table-capacity-display">{{ $table->capacity }}p</span>
          </div>
        @endforeach
        </div>
      </div>

      <!-- Table Editor Panel -->
      <div id="table-editor-panel" role="dialog" aria-labelledby="panel-title" aria-hidden="true" style="display: none;">
        <div class="panel-header">
          <h3 id="panel-title">Edit Table</h3>
          <button id="close-panel-btn" aria-label="Close panel">×</button>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="table-name-input">Table Name / ID</label>
            <input type="text" id="table-name-input" placeholder="e.g., T-01" aria-describedby="table-name-help">
          </div>
          <div class="form-group">
            <label for="table-capacity-input">Capacity</label>
            <input type="number" id="table-capacity-input" min="1" placeholder="e.g., 4" aria-describedby="table-capacity-help">
          </div>
          <div class="form-group">
            <label for="table-shape-select">Table Shape</label>
            <select id="table-shape-select" aria-describedby="table-shape-help">
              <option value="square">Square</option>
              <option value="rectangle">Rectangle</option>
              <option value="round">Round</option>
              <option value="vvip">VVIP Room</option>
            </select>
          </div>
        </div>
        <div class="panel-footer">
          <button class="panel-btn" id="save-table-btn">Apply Changes</button>
          <button class="panel-btn-danger" id="delete-table-btn">Delete Table</button>
        </div>
      </div>

      <!-- Confirmation Modal -->
      <div id="confirm-modal" class="modal-overlay" style="display: none;">
        <div class="modal">
          <div class="modal-header">
            <h3 class="modal-title" id="confirm-title">Confirm Action</h3>
          </div>
          <div class="modal-body">
            <p id="confirm-message"></p>
          </div>
          <div class="modal-footer">
            <button class="admin-btn btn-secondary" id="confirm-cancel">Cancel</button>
            <button class="admin-btn btn-danger" id="confirm-ok">Confirm</button>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let isDragging = false;
    let draggedElement = null;
    let offset = { x: 0, y: 0 };
    let currentTable = null;

    // Log loaded table positions for debugging
    console.log('=== Table Layout Loaded ===');
    document.querySelectorAll('.layout-table').forEach(function(table) {
        const position = {
            id: table.dataset.tableId,
            name: table.textContent.replace(/\d+p/, '').trim(),
            left: table.style.left,
            top: table.style.top
        };
        console.log('Table loaded:', position);
    });

    // Make tables draggable
    function makeDraggable(element) {
        element.addEventListener('mousedown', function(e) {
            isDragging = true;
            draggedElement = this;
            const rect = this.getBoundingClientRect();
            const containerRect = document.getElementById('layout-editor-container').getBoundingClientRect();
            offset.x = e.clientX - rect.left;
            offset.y = e.clientY - rect.top;
            
            this.style.cursor = 'grabbing';
            e.preventDefault();
        });

        element.addEventListener('dblclick', function(e) {
            e.preventDefault();
            openEditPanel(this);
        });
    }

    // Initialize draggable for existing tables
    document.querySelectorAll('.layout-table').forEach(makeDraggable);

    // Mouse move handler
    document.addEventListener('mousemove', function(e) {
        if (!isDragging || !draggedElement) return;

        const container = document.getElementById('layout-editor-container');
        const containerRect = container.getBoundingClientRect();
        
        let newX = e.clientX - containerRect.left - offset.x;
        let newY = e.clientY - containerRect.top - offset.y;

        // Keep within container bounds
        newX = Math.max(0, Math.min(newX, container.offsetWidth - draggedElement.offsetWidth));
        newY = Math.max(0, Math.min(newY, container.offsetHeight - draggedElement.offsetHeight));

        draggedElement.style.left = newX + 'px';
        draggedElement.style.top = newY + 'px';
    });

    // Mouse up handler
    document.addEventListener('mouseup', function() {
        if (isDragging && draggedElement) {
            draggedElement.style.cursor = 'grab';
            isDragging = false;
            draggedElement = null;
        }
    });

    // Open edit panel
    function openEditPanel(tableElement) {
        currentTable = tableElement;
        const tableId = tableElement.dataset.tableId;
        
        // Get table name by removing the capacity span content
        const capacitySpan = tableElement.querySelector('.table-capacity-display');
        const fullText = tableElement.textContent;
        const capacityText = capacitySpan ? capacitySpan.textContent : '';
        const tableName = fullText.replace(capacityText, '').trim();
        
        const capacity = capacitySpan ? capacitySpan.textContent.replace('p', '') : '';
        
        document.getElementById('table-name-input').value = tableName;
        document.getElementById('table-capacity-input').value = capacity;
        
        // Set table shape based on class
        let shape = 'square';
        if (tableElement.classList.contains('rectangle')) shape = 'rectangle';
        else if (tableElement.classList.contains('round')) shape = 'round';
        else if (tableElement.classList.contains('vvip')) shape = 'vvip';
        document.getElementById('table-shape-select').value = shape;
        
        // Change panel title for editing
        document.getElementById('panel-title').textContent = 'Edit Table';
        
        document.getElementById('table-editor-panel').style.display = 'flex';
        document.getElementById('table-editor-panel').setAttribute('aria-hidden', 'false');
    }

    // Close panel
    document.getElementById('close-panel-btn').addEventListener('click', function() {
        document.getElementById('table-editor-panel').style.display = 'none';
        document.getElementById('table-editor-panel').setAttribute('aria-hidden', 'true');
        currentTable = null;
    });

    // Save table changes
    document.getElementById('save-table-btn').addEventListener('click', function() {
        const tableName = document.getElementById('table-name-input').value;
        const capacity = document.getElementById('table-capacity-input').value;
        const shape = document.getElementById('table-shape-select').value;

        if (!tableName || !capacity) {
            showNotification('Please fill all required fields', 'error');
            return;
        }

        if (currentTable) {
            // Edit existing table
            const tableId = currentTable.dataset.tableId;

            // Update table display
            currentTable.innerHTML = tableName + '<span class="table-capacity-display">' + capacity + 'p</span>';
            
            // Update table class
            currentTable.className = 'layout-table ' + (shape === 'vvip' ? 'vvip' : shape);

            // Save to server
            fetch(`{{ route('admin.api.table-layouts.update-table', ':id') }}`.replace(':id', tableId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    table_number: tableName,
                    capacity: capacity,
                    table_type: shape === 'vvip' ? 'vip' : (shape === 'rectangle' ? 'outdoor' : (shape === 'round' ? 'indoor' : 'indoor'))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Table updated successfully', 'success');
                } else {
                    showNotification('Error updating table', 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating table', 'error');
            });
        } else {
            // Add new table - map shape to table_type
            const tableType = shape === 'vvip' ? 'vip' : (shape === 'rectangle' ? 'outdoor' : (shape === 'round' ? 'indoor' : 'indoor'));

            fetch('{{ route("admin.api.table-layouts.add-table") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    table_number: tableName,
                    capacity: capacity,
                    table_type: tableType,
                    x: 50,
                    y: 50
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Create new table element
                    const newTable = document.createElement('div');
                    newTable.className = 'layout-table ' + shape;
                    newTable.id = 'table-' + data.table.id;
                    newTable.dataset.tableId = data.table.id;
                    newTable.style.left = '50px';
                    newTable.style.top = '50px';
                    newTable.innerHTML = tableName + '<span class="table-capacity-display">' + capacity + 'p</span>';
                    
                    document.getElementById('layout-editor-container').appendChild(newTable);
                    makeDraggable(newTable);
                    
                    showNotification('Table added successfully', 'success');
                } else {
                    showNotification('Error adding table', 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding table', 'error');
            });
        }

        document.getElementById('table-editor-panel').style.display = 'none';
    });

    // Delete table
    document.getElementById('delete-table-btn').addEventListener('click', function() {
        if (!currentTable) return;

        showConfirm('Delete Table', 'Are you sure you want to delete this table? This action cannot be undone.', function() {
            const tableId = currentTable.dataset.tableId;
            deleteTable(tableId);
        });
    });

    function deleteTable(tableId) {

        fetch(`{{ route('admin.api.table-layouts.delete-table', ':id') }}`.replace(':id', tableId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentTable.remove();
                showNotification('Table deleted successfully', 'success');
                document.getElementById('table-editor-panel').style.display = 'none';
            } else {
                showNotification('Error deleting table', 'error');
            }
        })
        .catch(error => {
            showNotification('Error deleting table', 'error');
        });
    }

    // Save layout
    document.getElementById('save-layout-btn').addEventListener('click', function() {
        const container = document.getElementById('layout-editor-container');
        const tables = [];

        document.querySelectorAll('.layout-table').forEach(function(table) {
            if (table.dataset.tableId) {
                const tableData = {
                    id: table.dataset.tableId,
                    x: parseInt(table.style.left.replace('px', '')) || 0,
                    y: parseInt(table.style.top.replace('px', '')) || 0
                };
                tables.push(tableData);
                console.log('Saving table:', tableData);
            }
        });

        // Get container dimensions
        const containerWidth = container.offsetWidth;
        const containerHeight = container.offsetHeight;

        console.log('Sending layout data:', {
            tables: tables,
            container_width: containerWidth,
            container_height: containerHeight
        });

        fetch('{{ route("admin.api.table-layouts.save-layout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tables: tables,
                container_width: containerWidth,
                container_height: containerHeight
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Save response:', data);
            if (data.success) {
                showNotification(`Layout saved successfully (${data.saved_count} tables, ${containerWidth}×${containerHeight}px)`, 'success');
                // Optional: Reload page to verify save
                // setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error saving layout: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showNotification('Error saving layout', 'error');
        });
    });

    // Add new table buttons
    document.getElementById('add-square-table-btn').addEventListener('click', function() {
        addNewTable('square');
    });

    document.getElementById('add-rectangle-table-btn').addEventListener('click', function() {
        addNewTable('rectangle');
    });

    document.getElementById('add-vvip-room-btn').addEventListener('click', function() {
        addNewTable('vvip');
    });

    // Add new table function
    function addNewTable(shape) {
        currentTable = null; // Mark as new table
        
        // Clear the form
        document.getElementById('table-name-input').value = '';
        document.getElementById('table-capacity-input').value = '';
        
        // Set default shape
        const shapeValue = shape === 'vvip' ? 'vvip' : shape;
        document.getElementById('table-shape-select').value = shapeValue;
        
        // Change panel title for adding
        document.getElementById('panel-title').textContent = 'Add New Table';
        
        // Show the modal
        document.getElementById('table-editor-panel').style.display = 'flex';
        document.getElementById('table-editor-panel').setAttribute('aria-hidden', 'false');
    }

    // Reset layout
    document.getElementById('reset-layout-btn').addEventListener('click', function() {
        showConfirm('Reset Layout', 'Are you sure you want to reset the layout? This will reload the page and discard any unsaved changes.', function() {
            // Reset all tables to their default grid positions
            const tables = document.querySelectorAll('.layout-table');
            tables.forEach(function(table, index) {
                const x = 30 + ((index % 5) * 110);
                const y = 30 + (Math.floor(index / 5) * 100);
                table.style.left = x + 'px';
                table.style.top = y + 'px';
            });

            showNotification('Layout reset to default positions. Click "Save Layout" to keep these changes.', 'success');
        });
    });

    // Notification function
    function showNotification(message, type) {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Modern confirmation modal
    function showConfirm(title, message, onConfirm) {
        const modal = document.getElementById('confirm-modal');
        const titleEl = document.getElementById('confirm-title');
        const messageEl = document.getElementById('confirm-message');
        const confirmBtn = document.getElementById('confirm-ok');
        const cancelBtn = document.getElementById('confirm-cancel');

        titleEl.textContent = title;
        messageEl.textContent = message;
        modal.style.display = 'flex';

        // Remove old listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        const newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

        // Add new listeners
        newConfirmBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            onConfirm();
        });

        newCancelBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
@endsection