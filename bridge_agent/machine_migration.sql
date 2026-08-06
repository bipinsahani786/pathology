-- ============================================================
-- Machine Integration Tables
-- Run this SQL on your PostgreSQL server to add machine tables
-- Generated for: pathology_saas database
-- Run via: docker compose exec db psql -U postgres -d pathology_saas -f /migration.sql
-- ============================================================

-- Table 1: machine_integrations
-- Stores configuration for each lab machine
CREATE TABLE IF NOT EXISTS machine_integrations (
    id BIGSERIAL PRIMARY KEY,
    company_id BIGINT NOT NULL,
    branch_id BIGINT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    brand VARCHAR(100) DEFAULT NULL,
    machine_type VARCHAR(20) NOT NULL CHECK (machine_type IN ('biochemistry','hematology','electrolyte','other')),
    connection_type VARCHAR(20) NOT NULL DEFAULT 'serial' CHECK (connection_type IN ('serial','tcp','http_push')),
    port_or_ip VARCHAR(100) DEFAULT NULL,
    baud_rate INTEGER DEFAULT 9600,
    tcp_port INTEGER DEFAULT NULL,
    protocol VARCHAR(20) NOT NULL DEFAULT 'astm' CHECK (protocol IN ('astm','hl7','custom')),
    test_mapping JSONB DEFAULT NULL,
    api_token VARCHAR(64) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_seen_at TIMESTAMP(0) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_machine_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_machine_integrations_company ON machine_integrations(company_id);
CREATE INDEX IF NOT EXISTS idx_machine_integrations_token ON machine_integrations(api_token);

-- Table 2: machine_result_logs
-- Audit trail of every result received from machines
CREATE TABLE IF NOT EXISTS machine_result_logs (
    id BIGSERIAL PRIMARY KEY,
    machine_integration_id BIGINT NOT NULL,
    company_id BIGINT NOT NULL,
    sample_id VARCHAR(100) DEFAULT NULL,
    invoice_id BIGINT DEFAULT NULL,
    raw_data TEXT DEFAULT NULL,
    parsed_data JSONB DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','matched','imported','unmatched','failed','simulated')),
    error_message TEXT DEFAULT NULL,
    imported_at TIMESTAMP(0) DEFAULT NULL,
    imported_by BIGINT DEFAULT NULL,
    created_at TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mrl_machine FOREIGN KEY (machine_integration_id) REFERENCES machine_integrations(id) ON DELETE CASCADE,
    CONSTRAINT fk_mrl_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_mrl_company_sample ON machine_result_logs(company_id, sample_id);
CREATE INDEX IF NOT EXISTS idx_mrl_company_status ON machine_result_logs(company_id, status);
CREATE INDEX IF NOT EXISTS idx_mrl_invoice ON machine_result_logs(invoice_id);

-- Record in Laravel migrations table so artisan knows it's done
INSERT INTO migrations (migration, batch)
SELECT '2026_08_03_100000_create_machine_integrations_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations)
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_08_03_100000_create_machine_integrations_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_08_03_100001_create_machine_result_logs_table', (SELECT MAX(batch) FROM migrations)
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_08_03_100001_create_machine_result_logs_table');

-- Done!
SELECT 'Machine integration tables created successfully!' AS result;
