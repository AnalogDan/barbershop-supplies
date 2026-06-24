<style>
    .product-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 200px;
        gap: 20px;
        align-items: center;
        margin: 40px 60px 40px 60px;
        text-align: center;
    }

    .header {
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }

    .name {
        transition: outline 0.3s ease;
    }

    .name:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }

    @media (max-width: 768px) {

        .product-grid {
            display: block;
            margin: 15px 10px;
        }

        .product-grid .header {
            display: none;
        }

        .product-grid>div:not(.header) {
            display: block;
        }

        .name,
        .email,
        .created-at {
            display: block;
            width: 100%;
            padding: 6px 0;
            text-align: left;
            box-sizing: border-box;
        }

        .created-at {
            border-bottom: 1px solid #000;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .name::before {
            content: "Name: ";
            font-weight: bold;
        }

        .email::before {
            content: "Email: ";
            font-weight: bold;
        }

        .created-at::before {
            content: "Created: ";
            font-weight: bold;
        }
    }
</style>

<div class="product-grid">
    <div class="header name">Name</div>
    <div class="header email">Email</div>
    <div class="header created-at">Created at</div>

    <?php if (!empty($users)): ?>
        <?php
        foreach ($users as $user) {
        ?>
            <div class="name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
            <div class="email"><?= htmlspecialchars($user['email']) ?></div>
            <div class="created-at"><?= date('m/d/Y', strtotime($user['created_at'])) ?></div>
        <?php
        }
        ?>
    <?php else: ?>
        <div class="no-orders" style="grid-column: 1 / -1; text-align: center; padding: 1rem;">
            No users found.
        </div>
    <?php endif; ?>
</div>