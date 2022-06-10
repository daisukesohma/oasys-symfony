import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const UPDATE_COMPANY = gql`
    mutation updateCompany (
        $id: String!,
        $name: String!,
        $salesforceLink: String,
        $file: Upload
    ) {
        updateCompany (
            id: $id,
            name: $name,
            salesforceLink: $salesforceLink
            file: $file
        ) {
            ...CompanyFragment
        }
    }
    ${COMPANY_FRAGMENT}
`;
