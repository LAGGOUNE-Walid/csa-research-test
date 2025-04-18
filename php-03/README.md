**Note: create sqlite database called users.db**
### Sql Query

```sql
SELECT id, name, email, created_at
FROM users
WHERE is_active = 1
ORDER BY created_at DESC
LIMIT 50;
```

---

### Indexing

To make the query efficient at scale, especially with millions of rows:

#### 🔹 Composite Index:

```sql
CREATE INDEX idx_is_active_created_at ON users (is_active, created_at DESC);
```

#### Why?

1. **`is_active`** is used in the WHERE so it should come first in the index.
2. **`created_at DESC`** is used in the `ORDER BY` so it comes **second** in the index.
3. `LIMIT` = 50 so our database don't have to scan the whole table.

---

### Other Tips

1. Create cache in redis or memcached to store 50 most recent records of active users and update the cache whenever needed
2. Create database view to store only this query
