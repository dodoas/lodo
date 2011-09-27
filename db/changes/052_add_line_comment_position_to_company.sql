ALTER TABLE `company` ADD COLUMN `InvoiceLineCommentPosition` varchar(10) NOT NULL DEFAULT 'bottom' AFTER `InvoiceCommentCustomerPosition`;

