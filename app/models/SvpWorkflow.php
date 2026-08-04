<?php

namespace App\Models;

class SvpWorkflow extends BaseModel
{
    public const POSTING_CHANNEL_PHILGEPS = 'philgeps';
    public const POSTING_CHANNEL_WEBSITE = 'pe_website';
    public const POSTING_CHANNEL_CONSPICUOUS = 'conspicuous_place';

    public function findRfqByParent(int $parentId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_rfqs
             WHERE parent_procurement_id = :parent_procurement_id
             LIMIT 1'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetch() ?: null;
    }

    public function createRfq(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_rfqs (
                parent_procurement_id,
                rfq_no,
                rfq_date,
                quotation_deadline,
                delivery_period,
                payment_terms,
                warranty_terms,
                technical_specs,
                terms_and_conditions,
                is_posting_required,
                issued_at,
                created_by,
                updated_by
            ) VALUES (
                :parent_procurement_id,
                :rfq_no,
                :rfq_date,
                :quotation_deadline,
                :delivery_period,
                :payment_terms,
                :warranty_terms,
                :technical_specs,
                :terms_and_conditions,
                :is_posting_required,
                :issued_at,
                :created_by,
                :updated_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateRfq(int $id, array $data): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE svp_rfqs
             SET rfq_no = :rfq_no,
                 rfq_date = :rfq_date,
                 quotation_deadline = :quotation_deadline,
                 delivery_period = :delivery_period,
                 payment_terms = :payment_terms,
                 warranty_terms = :warranty_terms,
                 technical_specs = :technical_specs,
                 terms_and_conditions = :terms_and_conditions,
                 is_posting_required = :is_posting_required,
                 updated_by = :updated_by,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'rfq_no' => $data['rfq_no'],
            'rfq_date' => $data['rfq_date'],
            'quotation_deadline' => $data['quotation_deadline'],
            'delivery_period' => $data['delivery_period'],
            'payment_terms' => $data['payment_terms'],
            'warranty_terms' => $data['warranty_terms'],
            'technical_specs' => $data['technical_specs'],
            'terms_and_conditions' => $data['terms_and_conditions'],
            'is_posting_required' => $data['is_posting_required'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function issueRfq(int $id, string $issuedAt, int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE svp_rfqs
             SET issued_at = :issued_at,
                 updated_by = :updated_by,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'issued_at' => $issuedAt,
            'updated_by' => $userId,
        ]);
    }

    public function postingsForRfq(int $rfqId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_rfq_postings
             WHERE svp_rfq_id = :svp_rfq_id
             ORDER BY posted_at ASC, id ASC'
        );
        $statement->execute(['svp_rfq_id' => $rfqId]);

        return $statement->fetchAll() ?: [];
    }

    public function findPostingByChannel(int $rfqId, string $channel): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_rfq_postings
             WHERE svp_rfq_id = :svp_rfq_id
               AND posting_channel = :posting_channel
             LIMIT 1'
        );
        $statement->execute([
            'svp_rfq_id' => $rfqId,
            'posting_channel' => $channel,
        ]);

        return $statement->fetch() ?: null;
    }

    public function upsertPosting(array $data): int
    {
        $existing = $this->findPostingByChannel((int) $data['svp_rfq_id'], (string) $data['posting_channel']);

        if ($existing) {
            $statement = $this->connection()->prepare(
                'UPDATE svp_rfq_postings
                 SET posting_reference = :posting_reference,
                     posted_at = :posted_at,
                     posting_end_at = :posting_end_at,
                     remarks = :remarks
                 WHERE id = :id'
            );
            $statement->execute([
                'id' => $existing['id'],
                'posting_reference' => $data['posting_reference'],
                'posted_at' => $data['posted_at'],
                'posting_end_at' => $data['posting_end_at'],
                'remarks' => $data['remarks'],
            ]);

            return (int) $existing['id'];
        }

        $statement = $this->connection()->prepare(
            'INSERT INTO svp_rfq_postings (
                svp_rfq_id,
                posting_channel,
                posting_reference,
                posted_at,
                posting_end_at,
                remarks,
                created_by
            ) VALUES (
                :svp_rfq_id,
                :posting_channel,
                :posting_reference,
                :posted_at,
                :posting_end_at,
                :remarks,
                :created_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function suppliersForParent(int $parentId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_suppliers
             WHERE parent_procurement_id = :parent_procurement_id
             ORDER BY supplier_name ASC, id ASC'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetchAll() ?: [];
    }

    public function findSupplierById(int $id): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_suppliers
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function createSupplier(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_suppliers (
                parent_procurement_id,
                supplier_name,
                tin_no,
                address,
                contact_person,
                email,
                phone,
                philgeps_registration_no,
                is_invited,
                invited_at,
                created_at,
                updated_at
            ) VALUES (
                :parent_procurement_id,
                :supplier_name,
                :tin_no,
                :address,
                :contact_person,
                :email,
                :phone,
                :philgeps_registration_no,
                :is_invited,
                :invited_at,
                NOW(),
                NOW()
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function markSupplierInvited(int $id, string $invitedAt): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE svp_suppliers
             SET is_invited = 1,
                 invited_at = :invited_at,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'invited_at' => $invitedAt,
        ]);
    }

    public function quotationsForParent(int $parentId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT q.*, s.supplier_name
             FROM svp_quotations q
             INNER JOIN svp_suppliers s ON s.id = q.supplier_id
             WHERE q.parent_procurement_id = :parent_procurement_id
             ORDER BY q.amount ASC, q.submission_time ASC, q.id ASC'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetchAll() ?: [];
    }

    public function findQuotationById(int $id): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_quotations
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findQuotationByParentAndSupplier(int $parentId, int $supplierId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_quotations
             WHERE parent_procurement_id = :parent_procurement_id
               AND supplier_id = :supplier_id
             LIMIT 1'
        );
        $statement->execute([
            'parent_procurement_id' => $parentId,
            'supplier_id' => $supplierId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function createQuotation(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_quotations (
                parent_procurement_id,
                supplier_id,
                quotation_no,
                quotation_date,
                amount,
                delivery_offer,
                warranty_offer,
                payment_offer,
                submission_time,
                is_late,
                is_responsive,
                responsiveness_notes,
                attachment_path,
                created_at,
                updated_at
            ) VALUES (
                :parent_procurement_id,
                :supplier_id,
                :quotation_no,
                :quotation_date,
                :amount,
                :delivery_offer,
                :warranty_offer,
                :payment_offer,
                :submission_time,
                :is_late,
                :is_responsive,
                :responsiveness_notes,
                :attachment_path,
                NOW(),
                NOW()
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateQuotationResponsiveness(int $id, int $isResponsive, string $notes): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE svp_quotations
             SET is_responsive = :is_responsive,
                 responsiveness_notes = :responsiveness_notes,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'is_responsive' => $isResponsive,
            'responsiveness_notes' => $notes,
        ]);
    }

    public function evaluationForParent(int $parentId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_evaluations
             WHERE parent_procurement_id = :parent_procurement_id
             LIMIT 1'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetch() ?: null;
    }

    public function evaluationItems(int $evaluationId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT i.*, q.supplier_id, s.supplier_name
             FROM svp_evaluation_items i
             INNER JOIN svp_quotations q ON q.id = i.quotation_id
             INNER JOIN svp_suppliers s ON s.id = q.supplier_id
             WHERE i.evaluation_id = :evaluation_id
             ORDER BY COALESCE(i.rank_no, 999999) ASC, i.id ASC'
        );
        $statement->execute(['evaluation_id' => $evaluationId]);

        return $statement->fetchAll() ?: [];
    }

    public function deleteEvaluationItems(int $evaluationId): void
    {
        $statement = $this->connection()->prepare(
            'DELETE FROM svp_evaluation_items
             WHERE evaluation_id = :evaluation_id'
        );
        $statement->execute(['evaluation_id' => $evaluationId]);
    }

    public function createEvaluation(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_evaluations (
                parent_procurement_id,
                evaluation_date,
                quotation_count,
                is_posting_compliant,
                is_supplier_invitation_compliant,
                exception_note,
                recommended_supplier_id,
                recommended_amount,
                recommendation_text,
                approved_by,
                created_by
            ) VALUES (
                :parent_procurement_id,
                :evaluation_date,
                :quotation_count,
                :is_posting_compliant,
                :is_supplier_invitation_compliant,
                :exception_note,
                :recommended_supplier_id,
                :recommended_amount,
                :recommendation_text,
                :approved_by,
                :created_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateEvaluation(int $id, array $data): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE svp_evaluations
             SET evaluation_date = :evaluation_date,
                 quotation_count = :quotation_count,
                 is_posting_compliant = :is_posting_compliant,
                 is_supplier_invitation_compliant = :is_supplier_invitation_compliant,
                 exception_note = :exception_note,
                 recommended_supplier_id = :recommended_supplier_id,
                 recommended_amount = :recommended_amount,
                 recommendation_text = :recommendation_text,
                 approved_by = :approved_by,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'evaluation_date' => $data['evaluation_date'],
            'quotation_count' => $data['quotation_count'],
            'is_posting_compliant' => $data['is_posting_compliant'],
            'is_supplier_invitation_compliant' => $data['is_supplier_invitation_compliant'],
            'exception_note' => $data['exception_note'],
            'recommended_supplier_id' => $data['recommended_supplier_id'],
            'recommended_amount' => $data['recommended_amount'],
            'recommendation_text' => $data['recommendation_text'],
            'approved_by' => $data['approved_by'],
        ]);
    }

    public function createEvaluationItem(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_evaluation_items (
                evaluation_id,
                quotation_id,
                rank_no,
                quoted_amount,
                is_calculated,
                is_responsive,
                remarks
            ) VALUES (
                :evaluation_id,
                :quotation_id,
                :rank_no,
                :quoted_amount,
                :is_calculated,
                :is_responsive,
                :remarks
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function awardForParent(int $parentId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_awards
             WHERE parent_procurement_id = :parent_procurement_id
             LIMIT 1'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetch() ?: null;
    }

    public function createAward(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_awards (
                parent_procurement_id,
                supplier_id,
                award_no,
                award_date,
                award_amount,
                award_type,
                remarks,
                created_by
            ) VALUES (
                :parent_procurement_id,
                :supplier_id,
                :award_no,
                :award_date,
                :award_amount,
                :award_type,
                :remarks,
                :created_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function contractForParent(int $parentId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_contracts
             WHERE parent_procurement_id = :parent_procurement_id
             LIMIT 1'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetch() ?: null;
    }

    public function createContract(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_contracts (
                parent_procurement_id,
                award_id,
                contract_no,
                contract_date,
                contract_amount,
                contract_type,
                file_path,
                created_by
            ) VALUES (
                :parent_procurement_id,
                :award_id,
                :contract_no,
                :contract_date,
                :contract_amount,
                :contract_type,
                :file_path,
                :created_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }

    public function ntpForParent(int $parentId): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM svp_ntps
             WHERE parent_procurement_id = :parent_procurement_id
             LIMIT 1'
        );
        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetch() ?: null;
    }

    public function createNtp(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO svp_ntps (
                parent_procurement_id,
                contract_id,
                ntp_no,
                ntp_date,
                remarks,
                created_by
            ) VALUES (
                :parent_procurement_id,
                :contract_id,
                :ntp_no,
                :ntp_date,
                :remarks,
                :created_by
            )'
        );
        $statement->execute($data);

        return (int) $this->connection()->lastInsertId();
    }
}
