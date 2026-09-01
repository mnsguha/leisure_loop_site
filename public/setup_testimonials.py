import mysql.connector

try:
    conn = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="leisure_loop_db"
    )
    cursor = conn.cursor()

    # 1. Create Table
    sql_create = """
    CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        tour_name VARCHAR(255) NOT NULL,
        quote_text TEXT NOT NULL,
        image_url VARCHAR(500) NOT NULL,
        rotation_angle INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    """
    cursor.execute(sql_create)
    print("Table 'testimonials' created successfully.")

    # 2. Check if data exists
    cursor.execute("SELECT COUNT(*) FROM testimonials")
    count = cursor.fetchone()[0]

    if count == 0:
        sql_insert = """
        INSERT INTO testimonials (client_name, tour_name, quote_text, image_url, rotation_angle, sort_order) VALUES 
        ('Michael & Sarah T.', 'Bespoke Ladakh Expedition', 'An impeccably orchestrated journey. The attention to detail in Ladakh was nothing short of miraculous. We never felt like tourists, only honored guests.', 'https://images.unsplash.com/photo-1596781226767-17b01777d13f?q=80&w=600', 0, 1),
        ('Elena Rodriguez', 'Himalayan Retreat', 'From private tea estates in Darjeeling to remote monasteries, Leisure Loop curated an experience that felt utterly exclusive and authentic.', 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=600', 3, 2),
        ('David & Emma C.', 'Kashmir Honeymoon Loop', 'We rejected standard tours for our honeymoon. The secluded stays and private guides in Kashmir redefined luxury travel for us.', 'https://images.unsplash.com/photo-1627894483216-2138af692e32?q=80&w=600', -2, 3)
        """
        cursor.execute(sql_insert)
        conn.commit()
        print("Table 'testimonials' seeded with default data.")
    else:
        print("Table already has data, skipping seed.")

except Exception as e:
    print(f"Error: {e}")
finally:
    if 'conn' in locals() and conn.is_connected():
        cursor.close()
        conn.close()
