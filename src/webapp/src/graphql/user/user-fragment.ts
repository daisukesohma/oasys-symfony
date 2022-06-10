import gql from 'graphql-tag'

export const USER_FRAGMENT = gql`
    fragment UserFragment on User {
        id,
        civility,
        firstName,
        lastName,
        status,
        programType,
        phone,
        email,
        address,
        linkedin,
        function,
        seniorityDate,
        birthDate,
        previousFunction,
        rolesByUsersRoles {
            id,
            name,
        },
        company {
            id,
            name,
        },
        type {
            id,
            label,
        },
        coach {
            id,
            firstName,
            lastName,
            email,
            linkedin,
            phone,
            profilePicture {
                id,
            },
        },
        profilePicture {
            id,
            name,
            size,
        },
        cvFile {
            id,
            name,
        },
        nFirstName,
        nLastName,
        nEmail,
        nPhone,
        service,
        department,
        ville,
        postCode,
        appointmentBooked,
        professionalCategory {
            id
            label
        }
        annualCompensation,
        hasReceivedWelcomeMail,
        coachSpeciality {
            id
        },
        hasBeenTransferred,
        userCodePostal,
        userDepartment,
        userCity
    }
`;
