import gql from 'graphql-tag'

export const CREATE_USER_FROM_OFFLINE_FORM = gql`
    mutation createUserFromOfflineForm (
        $linkId: String!
        $recaptchaToken: String!
        $firstName: String!
        $lastName: String!
        $email: String!
        $phone: String!
        $civility: String
        $address: String
        $linkedin: String
        $function: String
        $seniorityDate: String
        $previousFunction: String
        $service: String
        $birthDate: String
        $professionalCategory: String
        $annualCompensation: String
        $userCodePostal: String
        $userDepartment: String
        $userCity: String
        $workMode: String
    ) {
        createUserFromOfflineForm (
            linkId: $linkId
            recaptchaToken: $recaptchaToken
            firstName: $firstName
            lastName: $lastName
            email: $email
            phone: $phone
            civility: $civility
            address: $address
            linkedin: $linkedin
            function: $function
            seniorityDate: $seniorityDate
            previousFunction: $previousFunction
            service: $service
            birthDate: $birthDate
            professionalCategory: $professionalCategory
            annualCompensation: $annualCompensation
            userDepartment: $userDepartment
            userCity: $userCity
            userCodePostal: $userCodePostal
            workMode: $workMode
        )
    }
`;
