/**
 * Grocerific JavaScript - Frontend functionality
 */

class GrocerificApp {
    constructor() {
        this.apiBase = 'api.php';
        this.items = [];
        this.filteredItems = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadItems();
    }
    
    bindEvents() {
        // Add item form
        document.getElementById('addItemForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.addItem();
        });
        
        // Edit item form
        document.getElementById('editItemForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.updateItem();
        });
        
        // Search and filter
        document.getElementById('searchInput').addEventListener('input', () => this.filterItems());
        document.getElementById('categoryFilter').addEventListener('change', () => this.filterItems());
        
        // Modal events
        document.querySelector('.close').addEventListener('click', () => this.closeModal());
        document.getElementById('editModal').addEventListener('click', (e) => {
            if (e.target.id === 'editModal') {
                this.closeModal();
            }
        });
        
        // Escape key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }
    
    async loadItems() {
        try {
            this.showLoading(true);
            const response = await fetch(`${this.apiBase}?action=items`);
            const data = await response.json();
            
            if (data.success) {
                this.items = data.data;
                this.filteredItems = [...this.items];
                this.renderItems();
            } else {
                this.showMessage('Failed to load items', 'error');
            }
        } catch (error) {
            console.error('Error loading items:', error);
            this.showMessage('Error loading items', 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    async addItem() {
        try {
            const formData = new FormData(document.getElementById('addItemForm'));
            const itemData = {
                name: formData.get('name'),
                category: formData.get('category'),
                quantity: parseInt(formData.get('quantity')),
                price: parseFloat(formData.get('price')),
                description: formData.get('description')
            };
            
            this.showLoading(true);
            const response = await fetch(`${this.apiBase}?action=add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(itemData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Item added successfully!', 'success');
                document.getElementById('addItemForm').reset();
                this.loadItems(); // Reload items
            } else {
                this.showMessage(data.error || 'Failed to add item', 'error');
            }
        } catch (error) {
            console.error('Error adding item:', error);
            this.showMessage('Error adding item', 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    async updateItem() {
        try {
            const formData = new FormData(document.getElementById('editItemForm'));
            const itemData = {
                id: parseInt(formData.get('id')),
                name: formData.get('name'),
                category: formData.get('category'),
                quantity: parseInt(formData.get('quantity')),
                price: parseFloat(formData.get('price')),
                description: formData.get('description')
            };
            
            this.showLoading(true);
            const response = await fetch(`${this.apiBase}?action=update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(itemData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Item updated successfully!', 'success');
                this.closeModal();
                this.loadItems(); // Reload items
            } else {
                this.showMessage(data.error || 'Failed to update item', 'error');
            }
        } catch (error) {
            console.error('Error updating item:', error);
            this.showMessage('Error updating item', 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    async deleteItem(id, name) {
        if (!confirm(`Are you sure you want to delete "${name}"?`)) {
            return;
        }
        
        try {
            this.showLoading(true);
            const response = await fetch(`${this.apiBase}?action=delete&id=${id}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Item deleted successfully!', 'success');
                this.loadItems(); // Reload items
            } else {
                this.showMessage(data.error || 'Failed to delete item', 'error');
            }
        } catch (error) {
            console.error('Error deleting item:', error);
            this.showMessage('Error deleting item', 'error');
        } finally {
            this.showLoading(false);
        }
    }
    
    editItem(id) {
        const item = this.items.find(item => item.id == id);
        if (!item) return;
        
        // Populate form fields
        document.getElementById('editId').value = item.id;
        document.getElementById('editName').value = item.name;
        document.getElementById('editCategory').value = item.category;
        document.getElementById('editQuantity').value = item.quantity;
        document.getElementById('editPrice').value = item.price;
        document.getElementById('editDescription').value = item.description || '';
        
        // Show modal
        document.getElementById('editModal').style.display = 'block';
    }
    
    closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    
    filterItems() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        this.filteredItems = this.items.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(searchTerm) ||
                                item.description.toLowerCase().includes(searchTerm);
            const matchesCategory = !categoryFilter || item.category === categoryFilter;
            
            return matchesSearch && matchesCategory;
        });
        
        this.renderItems();
    }
    
    renderItems() {
        const container = document.getElementById('itemsContainer');
        
        if (this.filteredItems.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No items found</h3>
                    <p>Try adjusting your search or add some items to get started!</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.filteredItems.map(item => this.renderItemCard(item)).join('');
    }
    
    renderItemCard(item) {
        return `
            <div class="item-card">
                <div class="item-header">
                    <h3 class="item-name">${this.escapeHtml(item.name)}</h3>
                    <span class="item-category">${this.escapeHtml(item.category)}</span>
                </div>
                
                <div class="item-details">
                    <div class="item-detail">
                        <span>Quantity:</span>
                        <strong>${item.quantity}</strong>
                    </div>
                    <div class="item-detail">
                        <span>Price:</span>
                        <span class="item-price">$${parseFloat(item.price).toFixed(2)}</span>
                    </div>
                    ${item.description ? `
                        <div class="item-description">
                            ${this.escapeHtml(item.description)}
                        </div>
                    ` : ''}
                </div>
                
                <div class="item-actions">
                    <button class="btn btn-secondary btn-sm" onclick="app.editItem(${item.id})">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="app.deleteItem(${item.id}, '${this.escapeHtml(item.name).replace(/'/g, "\\'")}')">
                        Delete
                    </button>
                </div>
            </div>
        `;
    }
    
    showLoading(show) {
        document.getElementById('loading').classList.toggle('hidden', !show);
    }
    
    showMessage(message, type) {
        const messageEl = document.getElementById('message');
        messageEl.textContent = message;
        messageEl.className = `message ${type}`;
        messageEl.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageEl.classList.add('hidden');
        }, 5000);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new GrocerificApp();
});

// Handle form validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.item-form');
    forms.forEach(form => {
        form.addEventListener('input', (e) => {
            const target = e.target;
            if (target.type === 'number' && target.value < 0) {
                target.value = 0;
            }
        });
    });
});