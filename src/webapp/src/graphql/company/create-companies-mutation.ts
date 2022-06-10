import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const CREATE_COMPANY = gql`
    mutation createCompany (
        $name: String!,
        $salesforceLink: String,
        $file: Upload
    ) {
        createCompany (
            name: $name,
            salesforceLink: $salesforceLink,
            file: $file
        ) {
            ...CompanyFragment
        }
    }
    ${COMPANY_FRAGMENT}
`;