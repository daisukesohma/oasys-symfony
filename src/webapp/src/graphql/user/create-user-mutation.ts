import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const CREATE_USER = gql`
    mutation createUser (
        $firstName: String!,
        $lastName: String!,
        $email: String!,
        $phone: String!,
        $typeId: String!,
        $roleIds: [String!]!,
        $civility: String,
        $address: String,
        $linkedin: String,
        $function: String,
        $seniorityDate: String,
        $previousFunction: String,
        $companyId: String,
        $coachId: String,
        $profilePictureId: String,
        $status: Boolean,
        $programType: String,
        $nFirstName: String,
        $nLastName: String,
        $nEmail: String,
        $nPhone: String,
        $service: String,
        $ville: String,
        $department: String,
        $postCode: String,
        $birthDate: String,
        $cvFileId: String,
        $professionalCategory: String,
        $annualCompensation: String,
        $coachSpeciality: String,
        $userCodePostal: String,
        $userCity: String,
        $userDepartment: String
    ) {
        createUser (
            firstName: $firstName, 
            lastName: $lastName, 
            email: $email, 
            phone: $phone, 
            typeId: $typeId, 
            roleIds: $roleIds, 
            civility: $civility,
            address: $address,
            linkedin: $linkedin,
            function: $function,
            seniorityDate: $seniorityDate,
            previousFunction: $previousFunction,
            companyId: $companyId,
            coachId: $coachId, 
            profilePictureId: $profilePictureId,
            status: $status,
            programType: $programType
            nFirstName: $nFirstName,
            nLastName: $nLastName,
            nEmail: $nEmail,
            nPhone: $nPhone,
            service: $service,
            ville: $ville,
            department: $department,
            postCode: $postCode,
            birthDate: $birthDate,
            cvFileId: $cvFileId,
            professionalCategory: $professionalCategory,
            annualCompensation: $annualCompensation,
            coachSpeciality: $coachSpeciality,
            userDepartment: $userDepartment,
            userCity: $userCity,
            userCodePostal: $userCodePostal
        ) {
            ...UserFragment
        }
    }
    ${USER_FRAGMENT}
`;
