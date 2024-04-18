-- Table structure for table `code_responses`
--

CREATE TABLE `code_responses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `response` text DEFAULT NULL,
  `prompt` text DEFAULT NULL,
  `conversation_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;