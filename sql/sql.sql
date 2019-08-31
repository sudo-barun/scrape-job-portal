CREATE TABLE scrape_attempts (
  id SERIAL PRIMARY KEY,
  started_at TIMESTAMP NOT NULL,
  completed_at TIMESTAMP NULL
);


CREATE TABLE scrape_logs (
  id SERIAL NOT NULL,
  attempt_id REFERENCES scrape_attempts (id),
  job_portal varchar NOT NULL,
  "version" int NOT NULL,
  url varchar NOT NULL,
  started_at TIMESTAMP NOT NULL,
  completed_at TIMESTAMP NULL,
  success BOOLEAN NULL,
  request TEXT NOT NULL,
  response TEXT NULL,
  error TEXT NULL
);


