import { DataTypes, Model } from 'sequelize';
import sequelize from '../config/database.js';

class Vehicle extends Model {}

Vehicle.init({
    id: {
        type: DataTypes.BIGINT,
        primaryKey: true,
        autoIncrement: true
    },
    vehicle_type: {
        type: DataTypes.STRING,
        allowNull: true
    },
    vehicle_number: {
        type: DataTypes.STRING,
        allowNull: true
    },
    odometer_reading: {
        type: DataTypes.STRING,
        allowNull: true
    },
    mpg: {
        type: DataTypes.STRING,
        allowNull: true
    },
    fuel_tank_capacity: {
        type: DataTypes.STRING,
        allowNull: true
    },
    fuel_left: {
        type: DataTypes.STRING,
        allowNull: true
    },
    vehicle_image: {
        type: DataTypes.STRING,
        allowNull: true
    },
    company_id: {
        type: DataTypes.BIGINT,
        allowNull: false,
        references: {
            model: 'users',
            key: 'id'
        }
    },
    vehicle_id: {
        type: DataTypes.STRING,
        allowNull: true,
        unique: true
    },
    vin: {
        type: DataTypes.STRING,
        allowNull: true
    },
    make_year: {
        type: DataTypes.STRING,
        allowNull: true
    },
    make: {
        type: DataTypes.STRING,
        allowNull: true
    },
    model: {
        type: DataTypes.STRING,
        allowNull: true
    },
    fuel_type: {
        type: DataTypes.STRING,
        allowNull: true
    },
    license: {
        type: DataTypes.STRING,
        allowNull: true
    },
    license_plate_number: {
        type: DataTypes.STRING,
        allowNull: true
    },
    secondary_tank_capacity: {
        type: DataTypes.STRING,
        allowNull: true
    },
    description: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    odometer_reading_type: {
        type: DataTypes.STRING,
        allowNull: true
    },
    fuel_tank_type: {
        type: DataTypes.STRING,
        allowNull: true
    },
    owner_id: {
        type: DataTypes.BIGINT,
        allowNull: false
    },
    owner_type: {
        type: DataTypes.STRING,
        allowNull: false
    },
    reserve_fuel: {
        type: DataTypes.STRING,
        allowNull: true
    }
}, {
    sequelize,
    modelName: 'Vehicle',
    tableName: 'vehicles',
    timestamps: true,
    underscored: true,
    indexes: [
        {
            unique: true,
            fields: ['vin', 'owner_type']
        }
    ]
});

// Add associations
Vehicle.associate = (models) => {
    Vehicle.belongsToMany(models.User, {
        through: 'driver_vehicles',
        foreignKey: 'vehicle_id',
        otherKey: 'driver_id'
    });
};

export default Vehicle; 