<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ReviewList implements ArgumentInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;


    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function getCustomerReviewEmailCollection(array $items): array
    {
        $customerEmails = [];

        foreach ($items as $review) {
            $customerId = (int) $review->getCustomerId();
            if (!$customerId) {
                continue;
            }

            $customerEmails[$customerId] = $this->getEmailById($customerId);
        }

        return $customerEmails;
    }

    private function getCustomerById(int $id): CustomerInterface
    {
        return $this->customerRepositoryInterface->getById($id);
    }

    public function getEmailById(int $id): string
    {
        $customer = $this->getCustomerById($id);
        return $customer ? $customer->getEmail() : '';
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCustomerReviewsGraphQlQuery()
    {
        return '
                reviews (
                        pageSize: %pageSize%
                        currentPage: %currentPage%
                    ){
                    items {
                        created_at
                        product {
                            name
                            image {
                                url
                                label
                            }
                            url_key
                        }
                        ratings_breakdown {
                            name
                            value
                        }
                        summary
                        text
                    }
                    page_info {
                        page_size
                        current_page
                        total_pages
                    }
                }
          ';
    }
}
