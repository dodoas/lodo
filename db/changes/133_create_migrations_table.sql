CREATE TABLE IF NOT EXISTS migrations (
  MigrationID INTEGER UNSIGNED PRIMARY KEY,
  MigrationName VARCHAR(255) NOT NULL,
  Status VARCHAR(50) DEFAULT "STARTED",
  StartedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  SucceededAt TIMESTAMP
)