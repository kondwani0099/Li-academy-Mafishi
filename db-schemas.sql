CREATE TABLE image_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    response TEXT,
    prompt TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE image_responses
ADD COLUMN prompt TEXT;


CREATE TABLE code_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    response TEXT,
    prompt TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) ,
    last_name VARCHAR(255),
    username VARCHAR(255) ,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    programme VARCHAR(255) ,
    password VARCHAR(255) NOT NULL
);